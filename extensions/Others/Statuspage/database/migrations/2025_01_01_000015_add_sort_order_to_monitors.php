<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// 4a9888356c4aff2772ee25ae76b8beb5

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ext_statuspage_monitors', function (Blueprint $table) {
            if (!Schema::hasColumn('ext_statuspage_monitors', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('category');
            }
        });

        // Set initial sort_order based on current order
        $monitors = DB::table('ext_statuspage_monitors')->orderBy('id')->get();
        foreach ($monitors as $index => $monitor) {
            DB::table('ext_statuspage_monitors')
                ->where('id', $monitor->id)
                ->update(['sort_order' => $index + 1]);
        }
    }

    public function down(): void
    {
        Schema::table('ext_statuspage_monitors', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
