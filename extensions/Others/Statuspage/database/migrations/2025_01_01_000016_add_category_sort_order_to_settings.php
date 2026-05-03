<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// 0d70be696f1d1c8830a810733401bff3

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ext_statuspage_settings')) {
            Schema::table('ext_statuspage_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('ext_statuspage_settings', 'category_sort_order')) {
                    $table->json('category_sort_order')->nullable()->after('show_maintenance');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('ext_statuspage_settings', function (Blueprint $table) {
            $table->dropColumn('category_sort_order');
        });
    }
};
