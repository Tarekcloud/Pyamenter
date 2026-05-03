<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ext_statuspage_maintenance_updates')) {
            return;
        }

        Schema::create('ext_statuspage_maintenance_updates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('maintenance_id');
            $table->enum('status', ['scheduled', 'in_progress', 'investigating', 'monitoring', 'completed'])->default('in_progress');
            $table->text('message');
            $table->timestamps();
        });

        if (Schema::hasTable('ext_statuspage_maintenances')) {
            try {
                Schema::table('ext_statuspage_maintenance_updates', function (Blueprint $table) {
                    $table->foreign('maintenance_id')
                          ->references('id')
                          ->on('ext_statuspage_maintenances')
                          ->onDelete('cascade');
                });
            } catch (\Exception $e) {
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ext_statuspage_maintenance_updates');
    }
};
