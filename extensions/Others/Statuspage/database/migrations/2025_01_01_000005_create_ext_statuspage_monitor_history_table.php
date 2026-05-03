<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ext_statuspage_monitor_history')) {
            return;
        }

        Schema::create('ext_statuspage_monitor_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('monitor_id');

            $table->enum('status', ['up', 'down']);
            $table->timestamp('checked_at');
            $table->timestamps();
        });

        if (Schema::hasTable('ext_statuspage_monitors')) {
            try {
                Schema::table('ext_statuspage_monitor_history', function (Blueprint $table) {
                    $table->foreign('monitor_id')
                          ->references('id')
                          ->on('ext_statuspage_monitors')
                          ->onDelete('cascade');
                });
            } catch (\Exception $e) {
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ext_statuspage_monitor_history');
    }
};
