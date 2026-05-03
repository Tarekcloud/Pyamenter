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
                if (!Schema::hasColumn('ext_statuspage_settings', 'maintenance_color')) {
                    $table->string('maintenance_color')->default('#3b82f6')->after('degraded_color');
                }
            });

            // Update existing record if it exists
            $existing = DB::table('ext_statuspage_settings')->first();
            if ($existing) {
                DB::table('ext_statuspage_settings')->update([
                    'maintenance_color' => '#3b82f6',
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('ext_statuspage_settings', function (Blueprint $table) {
            $table->dropColumn('maintenance_color');
        });
    }
};
