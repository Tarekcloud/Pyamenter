<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ext_hc_articles', function (Blueprint $table) {
            $table->engine = 'InnoDB'; 
            $table->id();
            $table->foreignId('category_id')
                  ->constrained('ext_hc_categories')
                  ->onDelete('cascade');
            $table->string('title');
            $table->string('description');
            $table->text('content');
            $table->text('htmlcontent')->nullable();
            $table->text('rawcontent')->nullable();
            $table->boolean('is_active')->default(true);
            $table->dateTime('published_at')->nullable();
            $table->string('slug')->unique();
            $table->integer('helpful_yes')->default(0);
            $table->integer('helpful_no')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ext_hc_articles');
    }
};
