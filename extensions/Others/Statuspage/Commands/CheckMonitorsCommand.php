<?php

namespace Paymenter\Extensions\Others\Statuspage\Commands;

use Illuminate\Console\Command;
use Paymenter\Extensions\Others\Statuspage\Models\Monitor;
use Paymenter\Extensions\Others\Statuspage\Models\Incident;
use Paymenter\Extensions\Others\Statuspage\Models\StatusPageSettings;
use Illuminate\Support\Facades\Http;

class CheckMonitorsCommand extends Command
{
    protected $signature = 'statuspage:check';
    protected $description = 'Check all monitors and update incidents if needed';

    public function handle()
    {
        $monitors = Monitor::all();

        foreach ($monitors as $monitor) {
            $errorMsg = null;
            $status = $this->checkMonitor($monitor, $errorMsg);
            $this->updateStatus($monitor, $status, $errorMsg);

            $this->line(sprintf(
                "[%s] Monitor '%s' (%s) => %s",
                now()->toDateTimeString(),
                $monitor->name,
                $monitor->type,
                strtoupper($status)
            ));
        }

        $this->info('All monitors checked successfully.');
        return Command::SUCCESS;
    }

    private function checkMonitor(Monitor $monitor, ?string &$errorMsg): string
    {
        try {
            switch ($monitor->type) {
                case 'http':
                case 'keyword':
                    $response = Http::timeout($monitor->timeout)->get($monitor->url);
                    $statusCode = $response->status();

                    $found = $monitor->type === 'keyword' ? str_contains($response->body(), $monitor->keyword) : true;

                    if ($statusCode !== (int) ($monitor->response ?? 200)) {
                        $errorMsg = "HTTP {$statusCode}";
                        return 'down';
                    }

                    if (!$found) {
                        $errorMsg = "Keyword not found";
                        return 'down';
                    }

                    return 'up';

                case 'tcp':
                    $connection = @fsockopen($monitor->host, $monitor->port, $errno, $errstr, $monitor->timeout);

                    if ($connection) {
                        fclose($connection);
                        return 'up';
                    }

                    if (stripos($errstr, 'timed out') !== false) {
                        $errorMsg = "timeout of " . ($monitor->timeout * 1000) . "ms exceeded";
                    } elseif (stripos($errstr, 'Could not resolve host') !== false) {
                        $errorMsg = "Could not resolve host";
                    } else {
                        $errorMsg = $errstr;
                    }

                    return 'down';

                case 'ping':
                    return $this->checkPing($monitor, $errorMsg);

                case 'dns':
                    return $this->checkDns($monitor, $errorMsg);

                case 'ssl':
                    return $this->checkSsl($monitor, $errorMsg);

                default:
                    $errorMsg = "Unknown monitor type";
                    return 'down';
            }
        } catch (\Illuminate\Http\Client\RequestException $e) {
            if ($e->getCode() === 0) {
                $errorMsg = "timeout of " . ($monitor->timeout * 1000) . "ms exceeded";
            } elseif (stripos($e->getMessage(), 'Could not resolve host') !== false) {
                $errorMsg = "Could not resolve host";
            } else {
                $errorMsg = "HTTP " . $e->getCode();
            }
            return 'down';
        } catch (\Exception $e) {
            if (stripos($e->getMessage(), 'Could not resolve host') !== false) {
                $errorMsg = "Could not resolve host";
            } elseif (stripos($e->getMessage(), 'timed out') !== false) {
                $errorMsg = "timeout of " . ($monitor->timeout * 1000) . "ms exceeded";
            } else {
                $errorMsg = "Error: " . $e->getMessage();
            }
            return 'down';
        }
    }

    private function checkPing(Monitor $monitor, ?string &$errorMsg): string
    {
        if (!$monitor->host) {
            $errorMsg = "Host not configured";
            return 'down';
        }

        $command = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' 
            ? "ping -n 1 -w " . ($monitor->timeout * 1000) . " " . escapeshellarg($monitor->host)
            : "ping -c 1 -W " . $monitor->timeout . " " . escapeshellarg($monitor->host) . " 2>&1";

        $output = [];
        $returnVar = 0;
        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            return 'up';
        }

        $errorMsg = "Ping failed";
        if (!empty($output)) {
            $errorMsg = implode(' ', $output);
        }
        return 'down';
    }

    private function checkDns(Monitor $monitor, ?string &$errorMsg): string
    {
        if (!$monitor->host) {
            $errorMsg = "Domain not configured";
            return 'down';
        }

        $result = @dns_get_record($monitor->host, DNS_ANY);

        if ($result === false || empty($result)) {
            $errorMsg = "DNS lookup failed";
            return 'down';
        }

        return 'up';
    }

    private function checkSsl(Monitor $monitor, ?string &$errorMsg): string
    {
        $host = $monitor->host ?? parse_url($monitor->url ?? '', PHP_URL_HOST);
        $port = 443;

        if (!$host) {
            $errorMsg = "Host not configured";
            return 'down';
        }

        if ($monitor->url) {
            $parsed = parse_url($monitor->url);
            $host = $parsed['host'] ?? $host;
            $port = $parsed['port'] ?? 443;
        }

        $context = stream_context_create([
            'ssl' => [
                'capture_peer_cert' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        $socket = @stream_socket_client(
            "ssl://{$host}:{$port}",
            $errno,
            $errstr,
            $monitor->timeout,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (!$socket) {
            $errorMsg = $errstr ?: "SSL connection failed";
            return 'down';
        }

        $params = stream_context_get_params($socket);
        $cert = $params['options']['ssl']['peer_certificate'] ?? null;

        fclose($socket);

        if (!$cert) {
            $errorMsg = "Certificate not found";
            return 'down';
        }

        $certData = openssl_x509_parse($cert);
        if (!$certData) {
            $errorMsg = "Failed to parse certificate";
            return 'down';
        }

        $validTo = $certData['validTo_time_t'] ?? 0;
        if ($validTo < time()) {
            $errorMsg = "Certificate expired";
            return 'down';
        }

        return 'up';
    }

    private function updateStatus(Monitor $monitor, string $status, ?string $errorMsg = null)
    {
        $previousStatus = $monitor->last_status;

        $monitor->update([
            'last_status' => $status,
            'last_checked_at' => now(),
        ]);

        $monitor->history()->create([
            'status' => $status,
            'checked_at' => now(),
        ]);

        $settings = StatusPageSettings::getSettings();
        $thresholdDate = now();
        if ($settings->history_type === 'hours') {
            $hours = (int)$settings->history_days;
            $thresholdDate = now()->subHours($hours + 24);
        } else {
            $thresholdDate = now()->subDays($settings->history_days + 1);
        }
        
        $monitor->history()
            ->where('checked_at', '<', $thresholdDate)
            ->delete();

        
        if ($previousStatus === 'up' && $status === 'down') {
            $this->warn("   ↳ Incident created for monitor '{$monitor->name}'");
            Incident::create([
                'monitor_id' => $monitor->id,
                'title' => "Monitor down: " . $monitor->name,
                'slug' => 'incident-' . uniqid(),
                'description' => "Monitor detected outage automatically.",
                'status' => 'investigating',
                'started_at' => now(),
            ]);

            $this->sendDiscordNotification($monitor, 'down', $errorMsg);
        }

        if ($previousStatus === 'down' && $status === 'up') {
            $incident = Incident::where('monitor_id', $monitor->id)
                ->whereNull('resolved_at')
                ->latest()
                ->first();

            if ($incident) {
                $incident->update([
                    'status' => 'resolved',
                    'resolved_at' => now(),
                ]);
                $this->info("   ↳ Incident resolved for monitor '{$monitor->name}'");
            }

            $this->sendDiscordNotification($monitor, 'up');
        }
    }

    private function sendDiscordNotification(Monitor $monitor, string $status, string $errorMsg = null)
    {
        $notifications = $monitor->notifications;

        foreach ($notifications as $notification) {
            $statusEmoji = $status === 'up' ? '✅' : '❌';
            
            $colorUp = $notification->embed_color_up ?? '#00FF00';
            $colorDown = $notification->embed_color_down ?? '#FF0000';
            $color = $status === 'up' ? $this->hexToDecimal($colorUp) : $this->hexToDecimal($colorDown);

            $embedTitle = $notification->embed_title ?? "{statusEmoji} {monitor} is {status}";
            $embedTitle = $this->replacePlaceholders($embedTitle, $monitor, $status, $statusEmoji, $errorMsg);

            $embedDescription = $notification->embed_description ?? null;
            if ($embedDescription) {
                $embedDescription = $this->replacePlaceholders($embedDescription, $monitor, $status, $statusEmoji, $errorMsg);
            }

            $fields = [];
            if ($notification->embed_fields && is_array($notification->embed_fields) && count($notification->embed_fields) > 0) {
                foreach ($notification->embed_fields as $field) {
                    $fields[] = [
                        'name' => $this->replacePlaceholders($field['name'] ?? '', $monitor, $status, $statusEmoji, $errorMsg),
                        'value' => $this->replacePlaceholders($field['value'] ?? '', $monitor, $status, $statusEmoji, $errorMsg),
                        'inline' => $field['inline'] ?? false,
                    ];
                }
            } else {
                $fields = [
                    [
                        'name' => 'Category',
                        'value' => $monitor->category ?? 'Uncategorized',
                        'inline' => true,
                    ],
                    [
                        'name' => 'URL / Host',
                        'value' => $monitor->type === 'tcp'
                            ? "{$monitor->host}:{$monitor->port}"
                            : $monitor->url,
                        'inline' => false,
                    ],
                    [
                        'name' => 'Last Checked At',
                        'value' => now()->toDateTimeString(),
                        'inline' => false,
                    ],
                ];

                if ($status === 'down' && $errorMsg) {
                    $fields[] = [
                        'name' => 'Error',
                        'value' => $errorMsg,
                        'inline' => false,
                    ];
                }
            }

            $embed = [
                'title' => $embedTitle,
                'color' => $color,
                'fields' => $fields,
                'timestamp' => now()->toIso8601String(),
            ];

            if ($embedDescription) {
                $embed['description'] = $embedDescription;
            }

            $payload = [
                'embeds' => [$embed],
            ];

            if ($notification->discord_tag) {
                $payload['content'] = $notification->discord_tag;
            }

            try {
                Http::post($notification->discord_webhook, $payload);
                $this->line("   ↳ Discord embed sent to {$notification->name}");
            } catch (\Exception $e) {
                $this->error("   ↳ Failed to send Discord embed: {$e->getMessage()}");
            }
        }
    }

    private function replacePlaceholders(string $text, Monitor $monitor, string $status, string $statusEmoji, ?string $errorMsg): string
    {
        $replacements = [
            '{monitor}' => $monitor->name,
            '{status}' => strtoupper($status),
            '{statusEmoji}' => $statusEmoji,
            '{category}' => $monitor->category ?? 'Uncategorized',
            '{url}' => $monitor->url ?? '',
            '{host}' => $monitor->host ?? '',
            '{port}' => $monitor->port ?? '',
            '{error}' => $errorMsg ?? '',
            '{uptime}' => number_format($monitor->uptime, 2) . '%',
            '{checked_at}' => now()->toDateTimeString(),
            '{type}' => strtoupper($monitor->type),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }

    private function hexToDecimal(string $hex): int
    {
        $hex = ltrim($hex, '#');
        
        if (strpos($hex, '0x') === 0) {
            $hex = substr($hex, 2);
        }
        
        return hexdec($hex);
    }
}
