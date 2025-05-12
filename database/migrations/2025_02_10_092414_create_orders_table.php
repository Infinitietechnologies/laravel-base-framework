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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('slug', 100)->unique();
            $table->string('email', 255);
            $table->string('attachments', 555);
            $table->string('ip_address', 45);
            $table->string('currency', 3)->nullable();
            $table->decimal('currency_rate', 10, 6);
            $table->bigInteger('currency_id')->nullable();
            $table->string('payment_method', 50);
            $table->string('payment_status', 50);
            $table->boolean('is_pos_order')->default(false);
            $table->string('created_via', 50);
            $table->decimal('wallet_balance', 12, 2);
            $table->decimal('merchant_wallet_balance', 12, 2);
            $table->string('promo_code', 50)->nullable();
            $table->decimal('promo_discount', 10, 2)->default(0.00);
            $table->string('gift_card', 50)->nullable();
            $table->decimal('gift_card_discount', 10, 2)->default(0.00);
            $table->decimal('reward_multiplier', 5, 2)->default(1.00);
            $table->boolean('is_delivery_charge_returnable')->default(false);
            $table->decimal('delivery_charge', 10, 2)->default(0.00);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total_payable', 12, 2);
            $table->decimal('final_total', 12, 2);

            // Billing Information
            $table->string('billing_name', 255);
            $table->text('billing_address_1');
            $table->text('billing_address_2')->nullable();
            $table->string('billing_landmark', 20)->nullable();
            $table->string('billing_zip', 20);
            $table->string('billing_phone', 20);
            $table->enum('billing_address_type', ['home', 'office', 'other']);
            $table->decimal('billing_latitude', 10, 8)->nullable();
            $table->decimal('billing_longitude', 11, 8)->nullable();
            $table->string('billing_city', 100);
            $table->string('billing_state', 100);
            $table->string('billing_country', 100);
            $table->string('billing_country_code', 3);

            // Shipping Information
            $table->string('shipping_name', 255);
            $table->text('shipping_address_1');
            $table->text('shipping_address_2')->nullable();
            $table->string('shipping_landmark', 20)->nullable();
            $table->string('shipping_zip', 20);
            $table->string('shipping_phone', 20);
            $table->enum('shipping_address_type', ['home', 'office', 'other']);
            $table->decimal('shipping_latitude', 10, 8)->nullable();
            $table->decimal('shipping_longitude', 11, 8)->nullable();
            $table->string('shipping_city', 100);
            $table->string('shipping_state', 100);
            $table->string('shipping_country', 100);
            $table->string('shipping_country_code', 3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
