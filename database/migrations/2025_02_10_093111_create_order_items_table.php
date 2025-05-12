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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->unsignedBigInteger('product_variant_id')->nullable();
            $table->foreign('product_variant_id')->references('id')->on('product_variants');
            $table->string('title', 255);
            $table->string('variant_title', 255)->nullable();
            $table->double('gift_card_discount', 10, 2)->default(0.00);
            $table->double('admin_commission_amount', 10, 2)->default(0.00);
            $table->double('merchant_commission_amount', 10, 2)->default(0.00);
            $table->enum('commission_settled', ['0', '1'])->default('0');
            $table->double('discounted_price', 10, 2)->default(0.00);
            $table->double('discount', 10, 2)->default(0.00);
            $table->string('hash_link', 500)->nullable();
            $table->string('tax_ids', 255)->nullable();
            $table->double('tax_amount')->nullable();
            $table->double('tax_percent')->nullable();
            $table->string('sku', 255);
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->string('status', 255);
            $table->string('active_status', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
