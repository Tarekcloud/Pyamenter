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
                $table->string('up_color')->default('#16a34a');
                $table->string('down_color')->default('#dc2626');
                $table->string('degraded_color')->default('#f59e0b');
                $table->integer('history_days')->default(30);
                $table->boolean('show_uptime_cards')->default(true);
                $table->timestamps();
            });

            if (DB::table('ext_statuspage_settings')->count() === 0) {
                DB::table('ext_statuspage_settings')->insert([
                    'up_color' => '#16a34a',
                    'down_color' => '#dc2626',
                    'degraded_color' => '#f59e0b',
                    'history_days' => 30,
                    'show_uptime_cards' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ext_statuspage_settings');
    }
};

