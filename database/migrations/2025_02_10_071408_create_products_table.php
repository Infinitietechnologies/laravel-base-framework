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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('merchant_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('product_condition_id');
            $table->foreign('merchant_id')->references('id')->on('merchants');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('brand_id')->references('id')->on('brands');
            $table->foreign('product_condition_id')->references('id')->on('product_conditions');
            $table->string('provider')->nullable();
            $table->bigInteger('provider_product_id')->nullable();
            $table->string('title');
            $table->string('slug',500);
            $table->integer('product_identity')->nullable()->unique();
            $table->enum('type', ['physical', 'digital']);
            $table->string('short_description');
            $table->text('description');
            $table->enum('indicator', ['veg', 'non_veg'])->nullable();
            $table->enum('download_allowed', ['0', '1'])->default('0');
            $table->string('download_link')->nullable();
            $table->integer('minimum_order_quantity')->default(1);
            $table->integer('quantity_step_size')->default(1);
            $table->integer('total_allowed_quantity')->default(1);
            $table->enum('is_inclusive_tax', ['0', '1'])->default('0');
            $table->enum('is_returnable', ['0', '1'])->default('0');
            $table->integer('returnable_days')->nullable();
            $table->enum('is_cancelable', ['0', '1'])->default('0');
            $table->enum('is_attachment_required', ['0', '1'])->default('0');
            $table->enum('status', ['active', 'draft'])->default('active');
            $table->enum('featured', ['0', '1'])->default('0');
            $table->enum('pickup_from_store', ['0', '1'])->default('1');
            $table->enum('home_delivery', ['0', '1'])->default('1');
            $table->enum('shipping', ['0', '1'])->default('1');
            $table->enum('in_store_purchase', ['0', '1'])->default('1');
            $table->enum('video_type', ['self_hosted', 'youtube', 'vimeo'])->nullable();
            $table->string('video_link')->nullable();
            $table->bigInteger('cloned_from_id')->nullable();
            $table->text('tags')->nullable();
            $table->string('warranty_period')->nullable();
            $table->string('guarantee_period')->nullable();
            $table->string('made_in')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
