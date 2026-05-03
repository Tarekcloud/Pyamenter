<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ext_statuspage_maintenances')) {
            Schema::create('ext_statuspage_maintenances', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->enum('status', ['scheduled', 'in_progress', 'investigating', 'monitoring', 'completed'])->default('scheduled');
                $table->timestamp('started_at')->nullable();
                $table->timestamp('scheduled_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('ext_statuspage_maintenances', function (Blueprint $table) {
                if (!Schema::hasColumn('ext_statuspage_maintenances', 'monitor_ids')) {
                    $table->json('monitor_ids')->nullable()->after('description');
                }
                if (!Schema::hasColumn('ext_statuspage_maintenances', 'color')) {
                    $afterColumn = Schema::hasColumn('ext_statuspage_maintenances', 'monitor_ids') 
                        ? 'monitor_ids' 
                        : 'description';
                    $table->string('color')->default('#3b82f6')->after($afterColumn);
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ext_statuspage_maintenances');
    }
};
