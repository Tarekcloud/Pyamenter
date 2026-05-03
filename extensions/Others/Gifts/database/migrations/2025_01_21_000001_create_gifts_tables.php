<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ext_gifts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type');
            $table->text('description')->nullable();
            
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->onDelete('cascade');
            $table->json('coupon_ids')->nullable();
            $table->boolean('allow_coupon_selection')->default(false);
            
            $table->decimal('credit_amount', 10, 2)->nullable();
            $table->decimal('credit_min_amount', 10, 2)->nullable();
            $table->decimal('credit_max_amount', 10, 2)->nullable();
            $table->boolean('allow_credit_range')->default(false);
            $table->string('currency_code')->nullable();
            $table->json('currency_codes')->nullable();
            $table->boolean('allow_currency_selection')->default(false);
            
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->foreignId('plan_id')->nullable()->constrained('plans')->onDelete('cascade');
            $table->integer('trial_period')->nullable();
            $table->string('trial_unit')->nullable();
            $table->integer('extension_period')->nullable();
            $table->integer('extension_min_period')->nullable();
            $table->integer('extension_max_period')->nullable();
            $table->boolean('allow_extension_range')->default(false);
            $table->string('extension_unit')->nullable();
            $table->foreignId('upgrade_product_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->foreignId('upgrade_plan_id')->nullable()->constrained('plans')->onDelete('cascade');
            
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('discount_min_amount', 10, 2)->nullable();
            $table->decimal('discount_max_amount', 10, 2)->nullable();
            $table->boolean('allow_discount_range')->default(false);
            $table->string('discount_type')->nullable();
            $table->string('discount_currency_code')->nullable();
            $table->decimal('discount_minimum_order', 10, 2)->nullable();
            $table->decimal('discount_maximum_discount', 10, 2)->nullable();
            $table->json('discount_product_ids')->nullable();
            $table->json('discount_category_ids')->nullable();
            $table->boolean('discount_applies_to_all')->default(true);
            
            $table->json('service_product_ids')->nullable();
            $table->json('service_plan_ids')->nullable();
            $table->boolean('allow_multiple_services')->default(false);
            
            $table->integer('max_uses')->nullable();
            $table->integer('max_uses_per_user')->default(1);
            $table->integer('used_count')->default(0);
            
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->boolean('allow_user_selection')->default(false);
            $table->timestamps();

            $table->index('code');
            $table->index('type');
            $table->index('is_active');
        });

        Schema::create('ext_gift_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gift_id')->constrained('ext_gifts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('selected_service_id')->nullable()->constrained('services')->onDelete('cascade');
            $table->foreignId('selected_product_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->foreignId('selected_plan_id')->nullable()->constrained('plans')->onDelete('cascade');
            $table->timestamp('redeemed_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('gift_id');
            $table->index('user_id');
            $table->index('redeemed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ext_gift_redemptions');
        Schema::dropIfExists('ext_gifts');
    }
};
