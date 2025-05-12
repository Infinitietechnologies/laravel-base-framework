<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The tables that need soft delete functionality.
     *
     * @var array
     */
    protected array $tables = [
        'addresses',
        'admin_payment_dues',
        'brands',
        'cart_items',
        'carts',
        'categories',
        'collections',
        'faqs',
        'merchant_feedback',
        'merchants',
        'order_items',
        'orders',
        'product_conditions',
        'products',
        'product_variants',
        'reviews',
        'shipping_parcels',
        'shipping_parcel_items',
        'store_product_variants',
        'stores',
        'tax_classes',
        'tax_rates',
        'users',
        'wallets'
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
