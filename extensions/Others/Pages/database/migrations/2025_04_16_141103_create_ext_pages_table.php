<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ext_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->boolean('visible')->default(true);
            $table->boolean('as_html')->default(false);
            $table->enum('visibility', ['public', 'client', 'admin'])->default('public');
            $table->enum('navigation', ['none', 'top', 'account_dropdown', 'dashboard'])->default('none');
            $table->unsignedTinyInteger('sort')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ext_pages');
    }
};
