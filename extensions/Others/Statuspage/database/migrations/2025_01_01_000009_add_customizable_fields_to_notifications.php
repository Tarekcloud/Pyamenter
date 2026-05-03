<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ext_statuspage_notifications')) {
            return;
        }

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
                $table->string('embed_color_up')->default('#00FF00')->after('embed_fields');
            }
            if (!Schema::hasColumn('ext_statuspage_notifications', 'embed_color_down')) {
                $table->string('embed_color_down')->default('#FF0000')->after('embed_color_up');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('ext_statuspage_notifications')) {
            return;
        }

        Schema::table('ext_statuspage_notifications', function (Blueprint $table) {
            $table->dropColumn([
                'embed_title',
                'embed_description',
                'embed_fields',
                'embed_color_up',
                'embed_color_down',
            ]);
        });
    }
};
