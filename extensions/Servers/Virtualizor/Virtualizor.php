<?php

namespace Paymenter\Extensions\Servers\Virtualizor;

use App\Classes\Extension\Server;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Virtualizor extends Server
{
    private function request($act, $method = 'get', $data = [], $clientApi = false): array
    {
        if ($clientApi) {
            $url = 'https://' . $this->config('ip') . ':' . $this->config('client_port') . '/index.php?api=json&adminapikey=' . $this->config('key') . '&adminapipass=' . $this->config('password') . '&act=' . $act;
        } else {
            $url = 'https://' . $this->config('ip') . ':' . $this->config('port') . '/index.php?api=json&adminapikey=' . $this->config('key') . '&adminapipass=' . $this->config('password') . '&act=' . $act;
        }

        // 1. 增加 120 秒超时设置，防止创建 VPS 时网络或磁盘 IO 耗时过长导致 cURL 28 错误
        // 2. 如果你的面板没有配置有效的 SSL 证书，请保留 withoutVerifying()，否则建议移除以提高安全性
        $httpClient = Http::timeout(120)->withoutVerifying();

        try {
            if ($method == 'get') {
                $url .= '&' . http_build_query($data);
                $response = $httpClient->get($url);
            } else {
                $response = $httpClient->asForm()->post($url, $data);
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new Exception('Virtualizor Connection Timeout or Network Error: ' . $e->getMessage());
        }

        // 优化错误抛出：直接将 Virtualizor 的具体报错内容返回给管理员，而不是笼统的 Failed
        if (!$response->successful()) {
            throw new Exception('Virtualizor HTTP Error [' . $response->status() . ']: ' . $response->body());
        }

        $json = $response->json();
        
        // 捕获 API 内部返回的错误 (有时 HTTP 状态是 200，但 JSON 里带有 error)
        if (isset($json['error']) && !empty($json['error'])) {
            $errorMsg = is_array($json['error']) ? implode(', ', $json['error']) : $json['error'];
            throw new Exception('Virtualizor API Error: ' . $errorMsg);
        }

        return $json;
    }

    public function getConfig($values = []): array
    {
        return [
            [
                'name' => 'key',
                'type' => 'text',
                'label' => 'API Key',
                'required' => true,
            ],
            [
                'name' => 'password',
                'type' => 'password', // 建议改为 password 类型保护隐私
                'label' => 'API Password',
                'required' => true,
            ],
            [
                'name' => 'ip',
                'type' => 'text',
                'label' => 'IP Address / Domain',
                'required' => true,
            ],
            [
                'name' => 'port',
                'type' => 'text',
                'label' => 'Port',
                'required' => true,
                'default' => '4085',
            ],
            [
                'name' => 'client_port',
                'type' => 'text',
                'label' => 'Client Port (normally 4083)',
                'default' => '4083',
                'required' => true,
            ],
        ];
    }

    public function getProductConfig($values = []): array
    {
        // 1. 获取所有计划 (Plans)
        $plansResp = $this->request('plans');
        $planOptions = [];
        if (!empty($plansResp['plans'])) {
            foreach ($plansResp['plans'] as $plan) {
                $virtType = $plan['virt'] ?? 'unknown';
                $planOptions[(string)$plan['plid']] = $plan['plan_name'] . ' (' . strtoupper($virtType) . ')';
            }
        }

        // 2. 获取宿主机列表 (Servers)
        $serversResp = $this->request('servers');
        $serverOptions = [
            '' => 'Auto (由Virtualizor自动选择节点)'
        ];
        if (!empty($serversResp['servers'])) {
            foreach ($serversResp['servers'] as $s) {
                $label = sprintf('#%s | %s | virt=%s | ip=%s',
                    $s['serid'] ?? 'n/a',
                    $s['server_name'] ?? 'unknown',
                    $s['virt'] ?? 'n/a',
                    $s['ip'] ?? '-'
                );
                $serverOptions[(string)($s['serid'] ?? '')] = $label;
            }
        }

        // 3. 动态获取存储列表 (Storage) - 提升体验，无需再手动填 ID
        $storageOptions = ['' => 'Auto (默认存储)'];
        try {
            $storageResp = $this->request('storage');
            if (!empty($storageResp['storage'])) {
                foreach ($storageResp['storage'] as $st) {
                    $storageOptions[(string)$st['stid']] = sprintf('%s (%s)', $st['name'], $st['path']);
                }
            }
        } catch (Exception $e) {
            // 获取失败时不阻断，静默失败即可
        }

        return [
            [
                'name' => 'plid',
                'label' => 'Plan Name (套餐计划)',
                'type' => 'select',
                'required' => true,
                'options' => $planOptions,
                'description' => '选择该产品的 Virtualizor 计划 (开通时将自动提取对应的虚拟化类型，无需手动配置)。'
            ],
            [
                'name' => 'serverid',
                'label' => 'Server (宿主机，可选)',
                'type' => 'select',
                'required' => false,
                'options' => $serverOptions,
                'default' => '',
                'description' => '选择具体宿主机。留空或选择 Auto 则由Virtualizor按规则自动分配。'
            ],
            [
                'name' => 'os_list',
                'label' => 'OS Templates (操作系统模板)',
                'type' => 'text',
                'required' => true,
                'description' => '格式：纯文本，逗号分隔的 "显示名称:osid" 对。例: Debian 12:101,Ubuntu 22.04:102'
            ],
            [
                'name' => 'stid',
                'label' => 'Storage (存储池，可选)',
                'type' => 'select', // 改为下拉选择
                'required' => false,
                'options' => $storageOptions,
                'default' => '',
                'description' => '指定要分配的存储池。留空则使用默认。'
            ],
        ];
    }

    public function getCheckoutConfig(Product $product)
    {
        $osListStr = $product->settings()->where('key', 'os_list')->first()?->value ?? '';
        $osOptions = [];

        if (!empty($osListStr)) {
            $osPairs = array_filter(explode(',', $osListStr));
            foreach ($osPairs as $pair) {
                $parts = explode(':', $pair);
                if (count($parts) >= 2) {
                    $osOptions[trim($parts[1])] = trim($parts[0]);
                }
            }
        }

        if (empty($osOptions)) {
            $osOptions['0'] = 'Please contact admin to configure OS List';
        }

        $generatedHostname = strtolower(Str::random(10)) . '.tarek';

        return [
            [
                'name' => 'hostname',
                'type' => 'text',
                // 修复正则：允许更标准的多级域名
                'validation' => 'regex:/^([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}$/',
                'label' => 'Hostname',
                'default' => $generatedHostname,
                'readonly' => true,
                'disabled' => true,
                'required' => true,
            ],
            [
                'name' => 'os',
                'type' => 'select',
                'friendlyName' => 'Operating System',
                'label' => 'Operating System',
                'required' => true,
                'options' => $osOptions,
            ],
        ];
    }

    public function testConfig(): bool|string
    {
        try {
            $this->request('users');
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return true;
    }

    private function getUser(User $user)
    {
        // 修正：如果用户已存在，也应该尝试捕获并验证
        $usersResp = $this->request('users', data: ['email' => $user->email]);
        if (!empty($usersResp['users'])) {
            return current($usersResp['users']);
        }
        
        $password = Str::password(16);
        $data = [
            'adduser' => 1,
            'priority' => 0,
            'newpass' => $password,
            'newemail' => $user->email,
            'fname' => $user->first_name ?: 'Client', // 容错处理：防止无名字导致建立失败
            'lname' => $user->last_name ?: $user->id,
        ];
        
        $response = $this->request('adduser', 'post', $data);

        // API 返回判断更加严谨
        if (empty($response['done']) || isset($response['error'])) {
            throw new Exception('Failed to create Virtualizor user.');
        }

        $usersResp = $this->request('users', data: ['email' => $user->email]);
        if (empty($usersResp['users'])) {
             throw new Exception('User created but cannot be fetched.');
        }
        
        $vUser = current($usersResp['users']);
        $vUser['password'] = $password;

        return $vUser;
    }

    public function createServer(Service $service, $settings, $properties)
    {
        $settings = array_merge($settings, $properties);

        if (empty($settings['plid'])) {
            throw new Exception('Plan ID (plid) is missing from product configuration.');
        }

        $plid = $settings['plid'];

        $plansResp = $this->request('plans', 'get', ['plid' => $plid]);
        $plan = null;
        if (!empty($plansResp['plans'])) {
            foreach ($plansResp['plans'] as $p) {
                if ($p['plid'] == $plid) {
                    $plan = $p;
                    break;
                }
            }
        }

        if (!$plan) {
            throw new Exception('Failed to fetch plan details or plan no longer exists.');
        }

        $virt = $plan['virt'] ?? null;
        if (empty($virt)) {
            throw new Exception('Failed to determine virtualization type (virt) from the selected plan.');
        }
        
        $password = Str::random(12);
        $user = $this->getUser($service->user);

        $isAutoNode = (!isset($settings['serverid']) || $settings['serverid'] === '__auto__' || $settings['serverid'] === '');
        $hostname = $settings['hostname'] ?? (strtolower(Str::random(10)) . '.tarek');
        $storageId = !empty($settings['stid']) ? trim($settings['stid']) : null;

        // 核心修复：全面使用 Null 合并运算符 ?? 避免 "Undefined array key" 报错
        $data = [
            'addvps' => 1,
            'node_select' => $isAutoNode ? 1 : 0, 
            'virt' => $virt,
            'uid' => $user['uid'],
            'osid' => $settings['os'] ?? '',
            'hostname' => $hostname,
            'rootpass' => $password,
            'slave_server' => $isAutoNode ? null : $settings['serverid'],
            'plid' => $plid,
            'stid' => $storageId,
            
            // 使用 ?? null 确保安全兜底，如果都没有就返回 null，在下方会被自动过滤
            'num_ips6' => $settings['ips6'] ?? $plan['ips6'] ?? null,
            'num_ips6_subnet' => $settings['ips6_subnet'] ?? $plan['ips6_subnet'] ?? null,
            'num_ips' => $settings['ips'] ?? $plan['ips'] ?? null,
            'ram' => $settings['ram'] ?? $plan['ram'] ?? null,
            'swapram' => $settings['swap'] ?? $plan['swap'] ?? null,
            'bandwidth' => $settings['bandwidth'] ?? $plan['bandwidth'] ?? null,
            'network_speed' => $settings['network_speed'] ?? $plan['network_speed'] ?? null,
            'cpu' => $settings['cpu'] ?? $plan['cpu'] ?? null,
            'cores' => $settings['cores'] ?? $plan['cores'] ?? null,
            'cpu_percent' => $settings['cpu_percent'] ?? $plan['cpu_percent'] ?? null,
            'vnc' => $settings['vnc'] ?? $plan['vnc'] ?? null,
            'kvm_cache' => $plan['kvm_cache'] ?? null,
            'io_mode' => $plan['io_mode'] ?? null,
            'vnc_keymap' => $plan['vnc_keymap'] ?? null,
            'nic_type' => $plan['nic_type'] ?? null,
            'osreinstall_limit' => $settings['osreinstall_limit'] ?? $plan['osreinstall_limit'] ?? null,
            'space' => $settings['space'] ?? $plan['space'] ?? null,
        ];

        // 移除所有 null 值，保留 0 或 false
        $data = array_filter($data, function ($v) { return !is_null($v); });

        $response = $this->request('addvs', 'post', $data);

        // error 已经被 request 里的统一拦截处理了，这里只需确保创建成功
        if (empty($response['newvs']['vpsid'])) {
            throw new Exception('VPS creation initialized but no VPS ID returned.');
        }

        $service->properties()->updateOrCreate(['key' => 'server_id'], [
            'name' => 'Virtualizor Server ID',
            'value' => $response['newvs']['vpsid'],
        ]);

        if ($storageId) {
            $service->properties()->updateOrCreate(['key' => 'stid'], [
                'name' => 'Storage ID',
                'value' => $storageId,
            ]);
        }

        $service->properties()->updateOrCreate(['key' => 'hostname'], [
            'name' => 'Hostname',
            'value' => $hostname,
        ]);

        // Attempt to fetch IP immediately if available, though it might take time to assign
        $vpsIp = 'Pending';
        if (isset($response['newvs']['ips']) && !empty($response['newvs']['ips'])) {
            // Virtualizor sometimes returns IPs in a nested array or comma-separated list
            $ips = $response['newvs']['ips'];
            if (is_array($ips)) {
                 $vpsIp = current($ips);
            } else {
                 // Assuming it might be comma separated if not array
                 $ipList = explode(',', $ips);
                 $vpsIp = trim($ipList[0]);
            }
            
             $service->properties()->updateOrCreate(['key' => 'vps_ip'], [
                'name' => 'VPS IP',
                'value' => $vpsIp,
            ]);
        }


        $response['newvs']['pass'] = $password;

        return [
            'vps' => $response['newvs'],
            'user' => $user,
        ];
    }

    public function suspendServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['server_id'])) {
            throw new Exception('Server does not exist');
        }
        $this->request('vs', 'post', ['suspend' => $properties['server_id']]); // 部分 API 使用 post 更安全
        return true;
    }

    public function unsuspendServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['server_id'])) {
            throw new Exception('Server does not exist');
        }
        $this->request('vs', 'post', ['unsuspend' => $properties['server_id']]);
        return true;
    }

    public function terminateServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['server_id'])) {
            throw new Exception('Server does not exist');
        }

        $request = $this->request('vs', 'post', ['delete' => $properties['server_id']]);

        if (empty($request['done']) || !$request['done']) {
            throw new Exception('Failed to terminate server');
        }

        $service->properties()->where('key', 'server_id')->delete();
        $service->properties()->where('key', 'vps_ip')->delete(); // Also cleanup IP

        return true;
    }

    public function getActions(Service $service, $settings, $properties): array
    {
        if (!isset($properties['server_id'])) {
            return [];
        }
        $hostname = $properties['hostname'] ?? $settings['hostname'] ?? 'Unknown';
        $vpsId = $properties['server_id'];
        
        $vpsIp = 'Fetching...';

        // Check if IP is already stored in properties to avoid API call on every page load
        if (isset($properties['vps_ip'])) {
            $vpsIp = $properties['vps_ip'];
        } else {
            // Attempt to fetch IP from Virtualizor API if not stored
            try {
                // Fetch specific VPS info
                $vsResp = $this->request('vs', 'get', ['vpsid' => $vpsId]);
                
                // Virtualizor API structure for listing VPS might vary slightly, 
                // but usually, it returns an array of vs or specific info.
                // Assuming 'vs' endpoint with vpsid parameter returns details under that ID or list.
                if (!empty($vsResp['vs'])) {
                     $vpsData = current($vsResp['vs']); // Get the first (and should be only) VPS
                     
                     if (isset($vpsData['ips']) && !empty($vpsData['ips'])) {
                         if(is_array($vpsData['ips'])) {
                             // Assuming standard format where ips is an array
                             $ipData = current($vpsData['ips']); // Might be nested depending on Virtualizor version
                             $vpsIp = is_array($ipData) && isset($ipData['ip']) ? $ipData['ip'] : (is_string($ipData) ? $ipData : 'N/A');
                         } else {
                             // Sometimes it's a string
                             $vpsIp = $vpsData['ips'];
                         }
                         
                         // Store it so we don't fetch every time
                         $service->properties()->updateOrCreate(['key' => 'vps_ip'], [
                            'name' => 'VPS IP',
                            'value' => $vpsIp,
                        ]);
                     } else {
                         $vpsIp = 'No IP Assigned';
                     }
                }
            } catch (Exception $e) {
                 $vpsIp = 'Error Fetching';
            }
        }


        return [
            [
                'type' => 'button',
                'label' => 'Go Control Panel',
                'function' => 'ssoLink',
            ],
            [
                'type' => 'button',
                'label' => 'Hostname: ' . $hostname,
                'function' => 'displayOnly', 
            ],
            [
                'type' => 'button',
                'label' => 'VPS IP: ' . $vpsIp, // Changed from VPS ID to VPS IP
                'function' => 'displayOnly', 
            ],
        ];
    }

    public function displayOnly(Service $service, $settings, $properties)
    {
        return null;
    }

    public function ssoLink(Service $service, $settings, $properties): string
    {
        if (!isset($properties['server_id'])) {
            throw new Exception('Server does not exist');
        }

        $response = $this->request('sso', data: ['svs' => $properties['server_id']], clientApi: true);

        if (!isset($response['sid'])) {
            throw new Exception('Failed to get Control Panel SSO link');
        }

        return 'https://' . $this->config('ip') . ':' . $this->config('client_port') . '/' . $response['token_key'] . '/?as=' . $response['sid'] . '&svs=' . $properties['server_id'];
    }

    public function upgradeServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['server_id'])) {
            throw new Exception('Server does not exist');
        }

        $settings = array_merge($settings, $properties);

        if (empty($settings['plid'])) {
            throw new Exception('Target Plan ID (plid) is missing.');
        }

        $editData = [
            'vpsid' => $properties['server_id'],
            'plid' => $settings['plid'],
            'theme_edit' => 1,
            'editvps' => 1,
        ];

        $response = $this->request('managevps', 'post', $editData);

        if (empty($response['done'])) {
            throw new Exception('Failed to upgrade server');
        }

        return true;
    }
}