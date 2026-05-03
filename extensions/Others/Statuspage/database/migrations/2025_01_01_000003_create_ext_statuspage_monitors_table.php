<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ext_statuspage_monitors')) {
            Schema::create('ext_statuspage_monitors', function (Blueprint $table) {
                $table->id();
                $table->text('category')->nullable();
                $table->string('name');
                $table->text('description')->nullable();
                $table->enum('type', ['http', 'keyword', 'tcp', 'ping', 'dns', 'ssl']);
                $table->string('url')->nullable();
                $table->string('host')->nullable();
                $table->unsignedInteger('port')->nullable();
                $table->string('keyword')->nullable();
                $table->unsignedInteger('response')->default(200);
                $table->unsignedInteger('interval')->default(60);
                $table->unsignedInteger('retries')->default(3);
                $table->unsignedInteger('timeout')->default(10);
                $table->enum('last_status', ['up', 'down'])->nullable();
                $table->timestamp('last_checked_at')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('ext_statuspage_monitors', function (Blueprint $table) {
                if (!Schema::hasColumn('ext_statuspage_monitors', 'sort_order')) {
                    $table->integer('sort_order')->default(0)->after('category');
                }
            });
        }

        if (Schema::hasTable('ext_statuspage_monitors') && 
            Schema::hasTable('ext_statuspage_notifications') && 
            !Schema::hasTable('ext_statuspage_monitor_notification')) {
            Schema::create('ext_statuspage_monitor_notification', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('monitor_id');
                $table->unsignedBigInteger('notification_id');
                $table->timestamps();
            });

            if (Schema::hasTable('ext_statuspage_monitors')) {
                try {
                    Schema::table('ext_statuspage_monitor_notification', function (Blueprint $table) {
                        $table->foreign('monitor_id')
                              ->references('id')
                              ->on('ext_statuspage_monitors')
                              ->onDelete('cascade');
                    });
                } catch (\Exception $e) {
                }
            }
            if (Schema::hasTable('ext_statuspage_notifications')) {
                try {
                    Schema::table('ext_statuspage_monitor_notification', function (Blueprint $table) {
                        $table->foreign('notification_id')
                              ->references('id')
                              ->on('ext_statuspage_notifications')
                              ->onDelete('cascade');
                    });
                } catch (\Exception $e) {
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ext_statuspage_monitor_notification');
        Schema::dropIfExists('ext_statuspage_monitors');
    }
};
