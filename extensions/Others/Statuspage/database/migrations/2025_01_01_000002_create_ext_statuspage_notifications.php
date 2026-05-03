<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ext_statuspage_notifications')) {
            Schema::create('ext_statuspage_notifications', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('discord_webhook');
                $table->string('discord_tag')->nullable();
                $table->text('embed_title')->nullable();
                $table->text('embed_description')->nullable();
                $table->text('embed_fields')->nullable();
                $table->string('embed_color_up')->default('0x00FF00');
                $table->string('embed_color_down')->default('0xFF0000');
                $table->timestamps();
            });
        } else {
            Schema::table('ext_statuspage_notifications', function (Blueprint $table) {
                if (!Schema::hasColumn('ext_statuspage_notifications', 'embed_title')) {
                    $table->text('embed_title')->nullable()->after('discord_tag');
                }
                if (!Schema::hasColumn('ext_statuspage_notifications', 'embed_description')) {
                    $table->text('embed_description')->nullable()->after('embed_title');
                }
                if (!Schema::hasColumn('ext_statuspage_notifications', 'embed_fields')) {
                    $table->text('embed_fields')->nullable()->after('embed_description');
                }
                if (!Schema::hasColumn('ext_statuspage_notifications', 'embed_color_up')) {
                    $table->string('embed_color_up')->default('0x00FF00')->after('embed_fields');
                }
                if (!Schema::hasColumn('ext_statuspage_notifications', 'embed_color_down')) {
                    $table->string('embed_color_down')->default('0xFF0000')->after('embed_color_up');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ext_statuspage_notifications');
    }
};
