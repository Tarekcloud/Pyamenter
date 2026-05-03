<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ext_statuspage_settings')) {
            Schema::create('ext_statuspage_settings', function (Blueprint $table) {
                $table->id();
                $table->enum('history_type', ['days', 'hours'])->default('days');
                $table->integer('history_days')->default(30);
                $table->integer('incidents_limit')->default(0);
                $table->integer('maintenance_limit')->default(0);
                $table->string('up_color')->default('#16a34a');
                $table->string('down_color')->default('#dc2626');
                $table->string('degraded_color')->default('#f59e0b');
                $table->boolean('show_uptime_cards')->default(true);
                $table->timestamps();
            });

            if (DB::table('ext_statuspage_settings')->count() === 0) {
                DB::table('ext_statuspage_settings')->insert([
                    'history_type' => 'days',
                    'history_days' => 30,
                    'incidents_limit' => 0,
                    'maintenance_limit' => 0,
                    'up_color' => '#16a34a',
                    'down_color' => '#dc2626',
                    'degraded_color' => '#f59e0b',
                    'show_uptime_cards' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } else {
            Schema::table('ext_statuspage_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('ext_statuspage_settings', 'history_type')) {
                    $table->enum('history_type', ['days', 'hours'])->default('days')->after('id');
                }
                if (!Schema::hasColumn('ext_statuspage_settings', 'incidents_limit')) {
                    $table->integer('incidents_limit')->default(0)->after('history_days');
                }
                if (!Schema::hasColumn('ext_statuspage_settings', 'maintenance_limit')) {
                    $table->integer('maintenance_limit')->default(0)->after('incidents_limit');
                }
            });

            $existing = DB::table('ext_statuspage_settings')->first();
            if ($existing) {
                DB::table('ext_statuspage_settings')->update([
                    'history_type' => $existing->history_type ?? 'days',
                    'incidents_limit' => $existing->incidents_limit ?? 0,
                    'maintenance_limit' => $existing->maintenance_limit ?? 0,
                ]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ext_statuspage_settings')) {
            Schema::table('ext_statuspage_settings', function (Blueprint $table) {
                if (Schema::hasColumn('ext_statuspage_settings', 'history_type')) {
                    $table->dropColumn('history_type');
                }
                if (Schema::hasColumn('ext_statuspage_settings', 'incidents_limit')) {
                    $table->dropColumn('incidents_limit');
                }
                if (Schema::hasColumn('ext_statuspage_settings', 'maintenance_limit')) {
                    $table->dropColumn('maintenance_limit');
                }
            });
        }
    }
};
