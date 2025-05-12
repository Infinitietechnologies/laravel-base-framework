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
        Schema::create('shipping_parcels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('store_id');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('store_id')->references('id')->on('stores');
            $table->bigInteger('shipment_id')->nullable();
            $table->bigInteger('external_shipment_id')->nullable();
            $table->bigInteger('carrier_id')->nullable();
            $table->bigInteger('manifest_id')->nullable();
            $table->string('manifest_url')->nullable();
            $table->string('service_code');
            $table->bigInteger('label_id')->nullable();
            $table->string('label_url')->nullable();
            $table->string('invoice_url')->nullable();
            $table->bigInteger('tracking_id');
            $table->string('tracking_url')->nullable();
            $table->string('shipment_cost_currency', 10);
            $table->decimal('shipment_cost', 5, 2);
            $table->float('weight')->nullable();
            $table->float('height')->nullable();
            $table->float('breadth')->nullable();
            $table->float('length')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_parcels');
    }
};
