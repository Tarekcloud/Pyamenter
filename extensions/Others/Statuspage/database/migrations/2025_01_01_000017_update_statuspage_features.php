<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ext_statuspage_maintenances')) {
            Schema::table('ext_statuspage_maintenances', function (Blueprint $table) {
                if (!Schema::hasColumn('ext_statuspage_maintenances', 'monitor_ids')) {
                    $table->json('monitor_ids')->nullable()->after('description');
                }
                if (!Schema::hasColumn('ext_statuspage_maintenances', 'color')) {
                    $table->string('color')->default('#3b82f6')->after('monitor_ids');
                }
            });
        }

        if (Schema::hasTable('ext_statuspage_settings')) {
            Schema::table('ext_statuspage_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('ext_statuspage_settings', 'maintenance_color')) {
                    $table->string('maintenance_color')->default('#3b82f6')->after('degraded_color');
                }
                if (!Schema::hasColumn('ext_statuspage_settings', 'show_incidents')) {
                    $table->boolean('show_incidents')->default(true)->after('show_uptime_cards');
                }
                if (!Schema::hasColumn('ext_statuspage_settings', 'show_maintenance')) {
                    $table->boolean('show_maintenance')->default(true)->after('show_incidents');
                }
                if (!Schema::hasColumn('ext_statuspage_settings', 'category_sort_order')) {
                    $table->json('category_sort_order')->nullable()->after('show_maintenance');
                }
            });

            $existing = DB::table('ext_statuspage_settings')->first();
            if ($existing) {
                $updateData = [];
                if (Schema::hasColumn('ext_statuspage_settings', 'maintenance_color') && !$existing->maintenance_color) {
                    $updateData['maintenance_color'] = '#3b82f6';
                }
                if (Schema::hasColumn('ext_statuspage_settings', 'show_incidents') && !isset($existing->show_incidents)) {
                    $updateData['show_incidents'] = true;
                }
                if (Schema::hasColumn('ext_statuspage_settings', 'show_maintenance') && !isset($existing->show_maintenance)) {
                    $updateData['show_maintenance'] = true;
                }
                if (!empty($updateData)) {
                    DB::table('ext_statuspage_settings')->update($updateData);
                }
            }
        }

        if (Schema::hasTable('ext_statuspage_monitors')) {
            Schema::table('ext_statuspage_monitors', function (Blueprint $table) {
                if (!Schema::hasColumn('ext_statuspage_monitors', 'sort_order')) {
                    $table->integer('sort_order')->default(0)->after('category');
                }
            });

            if (Schema::hasColumn('ext_statuspage_monitors', 'sort_order')) {
                $monitors = DB::table('ext_statuspage_monitors')
                    ->whereNull('sort_order')
                    ->orWhere('sort_order', 0)
                    ->orderBy('id')
                    ->get();
                foreach ($monitors as $index => $monitor) {
                    DB::table('ext_statuspage_monitors')
                        ->where('id', $monitor->id)
                        ->update(['sort_order' => $index + 1]);
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ext_statuspage_maintenances')) {
            Schema::table('ext_statuspage_maintenances', function (Blueprint $table) {
                if (Schema::hasColumn('ext_statuspage_maintenances', 'monitor_ids')) {
                    $table->dropColumn('monitor_ids');
                }
                if (Schema::hasColumn('ext_statuspage_maintenances', 'color')) {
                    $table->dropColumn('color');
                }
            });
        }

        if (Schema::hasTable('ext_statuspage_settings')) {
            Schema::table('ext_statuspage_settings', function (Blueprint $table) {
                if (Schema::hasColumn('ext_statuspage_settings', 'maintenance_color')) {
                    $table->dropColumn('maintenance_color');
                }
                if (Schema::hasColumn('ext_statuspage_settings', 'show_incidents')) {
                    $table->dropColumn('show_incidents');
                }
                if (Schema::hasColumn('ext_statuspage_settings', 'show_maintenance')) {
                    $table->dropColumn('show_maintenance');
                }
                if (Schema::hasColumn('ext_statuspage_settings', 'category_sort_order')) {
                    $table->dropColumn('category_sort_order');
                }
            });
        }

        if (Schema::hasTable('ext_statuspage_monitors')) {
            Schema::table('ext_statuspage_monitors', function (Blueprint $table) {
                if (Schema::hasColumn('ext_statuspage_monitors', 'sort_order')) {
                    $table->dropColumn('sort_order');
                }
            });
        }
    }
};
