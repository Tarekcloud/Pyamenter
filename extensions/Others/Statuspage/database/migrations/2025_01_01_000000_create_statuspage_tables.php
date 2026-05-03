<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ext_statuspage_categories')) {
            Schema::create('ext_statuspage_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ext_statuspage_notifications')) {
            Schema::create('ext_statuspage_notifications', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('discord_webhook');
                $table->string('discord_tag')->nullable();
                $table->text('embed_title')->nullable();
                $table->text('embed_description')->nullable();
                $table->text('embed_fields')->nullable();
                $table->string('embed_color_up')->default('0x00FF00');
                $table->string('embed_color_down')->default('0xFF0000');
                $table->timestamps();
            });
        } else {
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
                    $table->string('embed_color_up')->default('0x00FF00')->after('embed_fields');
                }
                if (!Schema::hasColumn('ext_statuspage_notifications', 'embed_color_down')) {
                    $table->string('embed_color_down')->default('0xFF0000')->after('embed_color_up');
                }
            });
        }

        if (!Schema::hasTable('ext_statuspage_monitors')) {
            Schema::create('ext_statuspage_monitors', function (Blueprint $table) {
                $table->id();
                $table->text('category')->nullable();
                $table->integer('sort_order')->default(0);
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

        if (!Schema::hasTable('ext_statuspage_incidents')) {
            Schema::create('ext_statuspage_incidents', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('monitor_id')->nullable();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->enum('status', ['investigating', 'monitoring', 'resolved'])->default('investigating');
                $table->timestamp('started_at')->nullable();
                $table->timestamp('resolved_at')->nullable();
                $table->timestamps();
            });

            if (Schema::hasTable('ext_statuspage_monitors')) {
                try {
                    Schema::table('ext_statuspage_incidents', function (Blueprint $table) {
                        $table->foreign('monitor_id')
                              ->references('id')
                              ->on('ext_statuspage_monitors')
                              ->onDelete('cascade');
                    });
                } catch (\Exception $e) {
                }
            }
        }

        if (!Schema::hasTable('ext_statuspage_monitor_history')) {
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
                $table->string('maintenance_color')->default('#3b82f6');
                $table->boolean('show_uptime_cards')->default(true);
                $table->boolean('show_incidents')->default(true);
                $table->boolean('show_maintenance')->default(true);
                $table->json('category_sort_order')->nullable();
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
                    'maintenance_color' => '#3b82f6',
                    'show_uptime_cards' => true,
                    'show_incidents' => true,
                    'show_maintenance' => true,
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

        if (!Schema::hasTable('ext_statuspage_maintenances')) {
            Schema::create('ext_statuspage_maintenances', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->json('monitor_ids')->nullable();
                $table->string('color')->default('#3b82f6');
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

        if (!Schema::hasTable('ext_statuspage_maintenance_updates')) {
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

        if (Schema::hasTable('ext_statuspage_monitors') && Schema::hasColumn('ext_statuspage_monitors', 'sort_order')) {
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

    public function down(): void
    {
        Schema::dropIfExists('ext_statuspage_maintenance_updates');
        Schema::dropIfExists('ext_statuspage_maintenances');
        Schema::dropIfExists('ext_statuspage_monitor_notification');
        Schema::dropIfExists('ext_statuspage_monitor_history');
        Schema::dropIfExists('ext_statuspage_incidents');
        Schema::dropIfExists('ext_statuspage_monitors');
        Schema::dropIfExists('ext_statuspage_settings');
        Schema::dropIfExists('ext_statuspage_notifications');
        Schema::dropIfExists('ext_statuspage_categories');
    }
};
