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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('merchant_id');
            $table->string('name');
            $table->string('slug',300);
            $table->string('address');
            $table->string('city');
            $table->string('landmark');
            $table->string('state');
            $table->string('zipcode');
            $table->string('country');
            $table->string('country_code');
            $table->decimal('latitude',10,8)->nullable();
            $table->decimal('longitude',11,8)->nullable();
            $table->string('contact_email',50);
            $table->integer('contact_number');
            $table->string('description')->nullable();
            $table->string('store_url')->nullable();
            $table->string('timing_varchar',500)->nullable();
            $table->text('address_proof');
            $table->text('voided_check');
            $table->string('tax_name');
            $table->string('tax_number');
            $table->string('bank_name');
            $table->string('bank_branch_code');
            $table->string('account_holder_name');
            $table->string('account_number');
            $table->string('routing_number');
            $table->enum('bank_account_type', ['checking', 'savings']);
            $table->string('currency_code')->default('USD');
            $table->text('permissions')->nullable();
            $table->boolean('pickup_from_store')->default(false);
            $table->boolean('home_delivery')->default(false);
            $table->boolean('shipping')->default(false);
            $table->boolean('in_store_purchase')->default(false);
            $table->text('time_slot_config')->nullable();
            $table->double('max_delivery_distance')->default(0);
            $table->double('shipping_min_free_delivery_amount')->default(0);
            $table->string('shipping_charge_priority')->nullable();
            $table->integer('allowed_order_per_time_slot')->nullable();
            $table->integer('order_preparation_time')->nullable();
            $table->text('pickup_time_schedule_config')->nullable();
            $table->text('carrier_partner')->nullable();
            $table->string('promotional_text',1024)->nullable();
            $table->double('restocking_percentage')->default(0);
            $table->boolean('shopify')->default(false);
            $table->text('shopify_settings')->nullable();
            $table->boolean('woocommerce')->default(false);
            $table->text('woocommerce_settings')->nullable();
            $table->boolean('etsy')->default(false);
            $table->text('etsy_settings')->nullable();
            $table->text('about_us')->nullable();
            $table->text('return_replacement_policy')->nullable();
            $table->text('refund_policy')->nullable();
            $table->text('terms_and_condition')->nullable();
            $table->text('delivery_policy')->nullable();
            $table->text('shipping_preference')->nullable();
            $table->double('domestic_shipping_charges')->default(0);
            $table->double('international_shipping_charges')->default(0);
            $table->string('meta_title')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->enum('verification_status', ['approved', 'not_approved'])->default('not_approved');
            $table->enum('visiblity_status', ['visible', 'draft'])->default('draft');
            $table->foreign('merchant_id')->references('id')->on('merchants');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
