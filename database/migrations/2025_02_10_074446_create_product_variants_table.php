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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->string('title');
            $table->string('slug')->unique();
            $table->float('weight');
            $table->float('height');
            $table->float('breadth');
            $table->float('length');
            $table->boolean('availablity')->default(true);
            $table->string('provider')->default('self');
            $table->string('provider_product_id')->nullable();
            $table->json('provider_json')->nullable();
            $table->string('barcode')->unique();
            $table->enum('visibility', ['published', 'draft']);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
