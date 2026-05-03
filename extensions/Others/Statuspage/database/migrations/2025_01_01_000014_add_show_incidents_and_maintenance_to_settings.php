<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// 4a9888356c4aff2772ee25ae76b8beb5

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ext_statuspage_settings')) {
            Schema::table('ext_statuspage_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('ext_statuspage_settings', 'show_incidents')) {
                    $table->boolean('show_incidents')->default(true)->after('show_uptime_cards');
                }
                if (!Schema::hasColumn('ext_statuspage_settings', 'show_maintenance')) {
                    $table->boolean('show_maintenance')->default(true)->after('show_incidents');
                }
            });

            // Update existing record if it exists
            $existing = DB::table('ext_statuspage_settings')->first();
            if ($existing) {
                DB::table('ext_statuspage_settings')->update([
                    'show_incidents' => true,
                    'show_maintenance' => true,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('ext_statuspage_settings', function (Blueprint $table) {
            $table->dropColumn(['show_incidents', 'show_maintenance']);
        });
    }
};
