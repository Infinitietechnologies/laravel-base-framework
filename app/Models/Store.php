<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Store\VerificationStatus;
use App\Enums\Store\VisiblityStatus;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [
        'merchant_id',
        'name',
        'slug',
        'address',
        'city',
        'landmark',
        'state',
        'zipcode',
        'country',
        'country_code',
        'latitude',
        'longitude',
        'contact_email',
        'contact_number',
        'description',
        'store_url',
        'timing_varchar',
        'address_proof',
        'voided_check',
        'tax_name',
        'tax_number',
        'bank_name',
        'bank_branch_code',
        'account_holder_name',
        'account_number',
        'routing_number',
        'bank_account_type',
        'currency_code',
        'permissions',
        'pickup_from_store',
        'home_delivery',
        'shipping',
        'in_store_purchase',
        'time_slot_config',
        'max_delivery_distance',
        'shipping_min_free_delivery_amount',
        'shipping_charge_priority',
        'allowed_order_per_time_slot',
        'order_preparation_time',
        'pickup_time_schedule_config',
        'carrier_partner',
        'promotional_text',
        'restocking_percentage',
        'shopify',
        'shopify_settings',
        'woocommerce',
        'woocommerce_settings',
        'etsy',
        'etsy_settings',
        'about_us',
        'return_replacement_policy',
        'refund_policy',
        'terms_and_condition',
        'delivery_policy',
        'shipping_preference',
        'domestic_shipping_charges',
        'international_shipping_charges',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'verification_status',
        'visiblity_status'
    ];

    protected $casts = [
        'verification_status' => VerificationStatus::class,
        'visiblity_status' => VisiblityStatus::class,
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
