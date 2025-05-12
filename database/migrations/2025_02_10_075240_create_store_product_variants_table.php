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
        Schema::create('store_product_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_variant_id');
            $table->unsignedBigInteger('store_id');
            $table->string('sku');
            $table->decimal('price',10,2);
            $table->decimal('special_price',10,2)->nullable();
            $table->decimal('cost',10,2)->nullable();
            $table->integer('stock')->default(0);
            $table->foreign('product_variant_id')->references('id')->on('product_variants');
            $table->foreign('store_id')->references('id')->on('stores');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_product_variants');
    }
};
