<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// 0d70be696f1d1c8830a810733401bff3

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ext_statuspage_maintenances', function (Blueprint $table) {
            $table->json('monitor_ids')->nullable()->after('description');
            $table->string('color')->default('#3b82f6')->after('monitor_ids');
        });
    }

    public function down(): void
    {
        Schema::table('ext_statuspage_maintenances', function (Blueprint $table) {
            $table->dropColumn(['monitor_ids', 'color']);
        });
    }
};
