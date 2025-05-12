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
        Schema::create('admin_payment_dues', function (Blueprint $table) {
            $table->id();
            $table->string('payment_type', 255);
            $table->unsignedBigInteger('merchant_id');
            $table->foreign('merchant_id')->references('id')->on('merchants');
            $table->string('currency_code', 10);
            $table->double('amount', 10, 2);
            $table->string('remark', 255)->nullable();
            $table->enum('status', ['processing', 'paid', 'failed', 'pending']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_payment_dues');
    }
};
