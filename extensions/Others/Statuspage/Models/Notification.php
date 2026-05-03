<?php

namespace Paymenter\Extensions\Others\Statuspage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Notification extends Model
{
    protected $table = 'ext_statuspage_notifications';

    protected $fillable = [
        'name',
        'description',
        'discord_webhook',
        'discord_tag',
        'embed_title',
        'embed_description',
        'embed_fields',
        'embed_color_up',
        'embed_color_down',
    ];

    protected $casts = [
        'embed_fields' => 'array',
    ];

    public function notify(string $message)
    {
        if (!$this->discord_webhook) return;

        $content = $this->discord_tag ? "{$this->discord_tag} " : '';
        $content .= $message;

        Http::post($this->discord_webhook, [
            'content' => $content,
        ]);
    }
}
