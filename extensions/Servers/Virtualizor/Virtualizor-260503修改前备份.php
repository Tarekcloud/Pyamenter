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

        if ($method == 'get') {
            $url .= '&' . http_build_query($data);
            $response = Http::withoutVerifying()->get($url)->throw();
        } elseif ($method == 'post') {
            $response = Http::withoutVerifying()->asForm()->$method($url, $data)->throw();
        }

        if (!$response->successful()) {
            throw new Exception('Failed to connect to Virtualizor API');
        }

        return $response->json();
    }

    /**
     * Get all the configuration for the extension
     *
     * @param  array  $values
     */
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
                'type' => 'text',
                'label' => 'API Password',
                'required' => true,
            ],
            [
                'name' => 'ip',
                'type' => 'text',
                'label' => 'IP Address',
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

    /**
     * Get product config
     *
     * @param  array  $values
     */
    public function getProductConfig($values = []): array
    {
        // 1. 获取所有计划 (Plans)
        $plansResp = $this->request('plans');
        $planOptions = [];
        if (!empty($plansResp['plans'])) {
            foreach ($plansResp['plans'] as $plan) {
                // 修复：API 中虚拟化类型字段通常为 virt
                $virtType = $plan['virt'] ?? 'unknown';
                // 显示 Plan Name，值为 plid
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
                'label' => 'Storage ID (可选)',
                'type' => 'text',
                'required' => false,
                'description' => '指定要分配的存储代号 (stid)'
            ],
        ];
    }

    public function getCheckoutConfig(Product $product)
    {
        // 从产品设置中读取已配置的 OS 列表纯文本并解析
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

        // 自动生成 10位小写字母数字 + .tarek
        $generatedHostname = strtolower(Str::random(10)) . '.tarek';

        $fields = [
            [
                'name' => 'hostname',
                'type' => 'text',
                'validation' => 'regex:/^[A-Za-z0-9-]+\.[A-Za-z0-9-]+$/',
                'label' => 'Hostname',
                'default' => $generatedHostname,
                'readonly' => true, // 锁定不可修改
                'disabled' => true, // 增加 disabled 以兼容不同版本的 Paymenter 前端只读效果
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

        return $fields;
    }

    /**
     * Check if currenct configuration is valid
     */
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
        $users = $this->request('users', data: ['email' => $user->email]);
        if (!empty($users['users'])) {
            return $users['users'][key($users['users'])];
        }
        $password = Str::password(16);
        // Create user
        $data = [
            'adduser' => 1,
            'priority' => 0,
            'newpass' => $password,
            'newemail' => $user->email,
            'fname' => $user->first_name,
            'lname' => $user->last_name,
        ];
        $response = $this->request('adduser', 'post', $data);

        if (!$response['done']) {
            throw new Exception('Failed to create user');
        }

        $users = $this->request('users', data: ['email' => $user->email]);
        $user = $users['users'][key($users['users'])];
        $user['password'] = $password;

        return $user;
    }

    /**
     * Create a server
     *
     * @param  array  $settings  (product settings)
     * @param  array  $properties  (checkout options)
     * @return bool
     */
    public function createServer(Service $service, $settings, $properties)
    {
        $settings = array_merge($settings, $properties);

        if (empty($settings['plid'])) {
            throw new Exception('Plan ID (plid) is missing from product configuration.');
        }

        $plid = $settings['plid'];

        // 精确获取所选 Plan 信息，用以推导 virt(ptype) 及各项默认资源
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

        // 从获取到的 Plan 中提取 virt 字段
        $virt = $plan['virt'] ?? null;
        if (empty($virt)) {
            throw new Exception('Failed to determine virtualization type (virt) from the selected plan.');
        }
        
        $password = Str::random(12);
        $user = $this->getUser($service->user);

        // 如果未选 serverid 或者是空字符串，则代表自动分配
        $isAutoNode = (!isset($settings['serverid']) || $settings['serverid'] === '__auto__' || $settings['serverid'] === '');

        // 防止前端用户恶意修改 hostname 或者未传值，做个兜底
        $hostname = $settings['hostname'] ?? (strtolower(Str::random(10)) . '.tarek');

        $storageId = !empty($settings['stid']) ? trim($settings['stid']) : null;

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
            
            // 完全还原您的默认继承 Plan 资源的逻辑
            'num_ips6' => isset($settings['ips6']) ? $settings['ips6'] : $plan['ips6'],
            'num_ips6_subnet' => isset($settings['ips6_subnet']) ? $settings['ips6_subnet'] : $plan['ips6_subnet'],
            'num_ips' => isset($settings['ips']) ? $settings['ips'] : $plan['ips'],
            'ram' => isset($settings['ram']) ? $settings['ram'] : $plan['ram'],
            'swapram' => isset($settings['swap']) ? $settings['swap'] : $plan['swap'],
            'bandwidth' => isset($settings['bandwidth']) ? $settings['bandwidth'] : $plan['bandwidth'],
            'network_speed' => isset($settings['network_speed']) ? $settings['network_speed'] : $plan['network_speed'],
            'cpu' => isset($settings['cpu']) ? $settings['cpu'] : $plan['cpu'],
            'cores' => isset($settings['cores']) ? $settings['cores'] : $plan['cores'],
            'cpu_percent' => isset($settings['cpu_percent']) ? $settings['cpu_percent'] : $plan['cpu_percent'],
            'vnc' => isset($settings['vnc']) ? $settings['vnc'] : $plan['vnc'],
            'kvm_cache' => $plan['kvm_cache'] ?? null,
            'io_mode' => $plan['io_mode'] ?? null,
            'vnc_keymap' => $plan['vnc_keymap'] ?? null,
            'nic_type' => $plan['nic_type'] ?? null,
            'osreinstall_limit' => isset($settings['osreinstall_limit']) ? $settings['osreinstall_limit'] : $plan['osreinstall_limit'],
            'space' => isset($settings['space']) ? $settings['space'] : $plan['space'],
        ];

        // 核心：移除所有 null 值的键。如果 ippid 是 null，它将不会被发送。
        $data = array_filter($data, function ($v) { return !is_null($v); });

        $response = $this->request('addvs', 'post', $data);

        if (isset($response['error']) && !empty($response['error'])) {
            $errorMsg = is_array($response['error']) ? implode(', ', $response['error']) : $response['error'];
            throw new Exception('Failed to create server with error: ' . $errorMsg);
        }

        $service->properties()->updateOrCreate([
            'key' => 'server_id',
        ], [
            'name' => 'Virtualizor Server ID',
            'value' => $response['newvs']['vpsid'],
        ]);

        if ($storageId) {
            $service->properties()->updateOrCreate(['key' => 'stid'], [
                'name' => 'Storage ID',
                'value' => $storageId,
            ]);
        }

        $service->properties()->updateOrCreate([
            'key' => 'hostname',
        ], [
            'name' => 'Hostname',
            'value' => $hostname,
        ]);

        ## 强制返回原始设置的VPS密码，用于邮件显示的密码
        $response['newvs']['pass'] = $password;

        return [
            'vps' => $response['newvs'],
            'user' => $user,
        ];
    }

    /**
     * Suspend a server
     *
     * @param  array  $settings  (product settings)
     * @param  array  $properties  (checkout options)
     * @return bool
     */
    public function suspendServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['server_id'])) {
            throw new Exception('Server does not exist');
        }

        // Suspend server
        $this->request('vs', 'get', ['suspend' => $properties['server_id']]);

        return true;
    }

    /**
     * Unsuspend a server
     *
     * @param  array  $settings  (product settings)
     * @param  array  $properties  (checkout options)
     * @return bool
     */
    public function unsuspendServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['server_id'])) {
            throw new Exception('Server does not exist');
        }

        // Unsuspend server
        $this->request('vs', 'get', ['unsuspend' => $properties['server_id']]);

        return true;
    }

    /**
     * Terminate a server
     *
     * @param  array  $settings  (product settings)
     * @param  array  $properties  (checkout options)
     * @return bool
     */
    public function terminateServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['server_id'])) {
            throw new Exception('Server does not exist');
        }

        // Terminate server
        $request = $this->request('vs', 'post', ['delete' => $properties['server_id']]);

        if (empty($request['done']) || !$request['done']) {
            throw new Exception('Failed to terminate server');
        }

        // Remove server id
        $service->properties()->where('key', 'server_id')->delete();

        return true;
    }

    public function getActions(Service $service, $settings, $properties): array
    {
        if (!isset($properties['server_id'])) {
            return [];
        }
        $hostname = $properties['hostname'] ?? $settings['hostname'] ?? 'Unknown';
        $vpsId = $properties['server_id'];

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
                'label' => 'VPS ID: ' . $vpsId,
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
            throw new Exception('Failed to get VNC link');
        }

        return 'https://' . $this->config('ip') . ':' . $this->config('client_port') . '/' . $response['token_key'] . '/?as=' . $response['sid'] . '&svs=' . $properties['server_id'];
    }

    public function upgradeServer(Service $service, $settings, $properties)
    {
        if (!isset($properties['server_id'])) {
            throw new Exception('Server does not exist');
        }

        $settings = array_merge($settings, $properties);

        // 由于已经直接配置了 plid，直接读取即可，不再需要进行额外查找匹配 plan_name
        if (empty($settings['plid'])) {
            throw new Exception('Target Plan ID (plid) is missing.');
        }

        $editData = [
            'vpsid' => $properties['server_id'],
            'plid' => $settings['plid'],
            'theme_edit' => 1, // Boolean
            'editvps' => 1, // Boolean
        ];

        $response = $this->request('managevps', 'post', $editData);

        if (empty($response['done'])) {
            throw new Exception('Failed to upgrade server');
        }

        return true;
    }
}