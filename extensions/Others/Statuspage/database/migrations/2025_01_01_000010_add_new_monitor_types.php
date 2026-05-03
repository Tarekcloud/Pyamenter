<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ext_statuspage_monitors')) {
            return;
        }

        try {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE ext_statuspage_monitors MODIFY COLUMN type ENUM('http', 'keyword', 'tcp', 'ping', 'dns', 'ssl')");
            } else {
                Schema::table('ext_statuspage_monitors', function (Blueprint $table) {
                    $table->enum('type', ['http', 'keyword', 'tcp', 'ping', 'dns', 'ssl'])->change();
                });
            }
        } catch (\Exception $e) {
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('ext_statuspage_monitors')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE ext_statuspage_monitors MODIFY COLUMN type ENUM('http', 'keyword', 'tcp')");
        } else {
            Schema::table('ext_statuspage_monitors', function (Blueprint $table) {
                $table->enum('type', ['http', 'keyword', 'tcp'])->change();
            });
        }
    }
};
