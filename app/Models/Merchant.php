<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Merchant extends Model
{
    use HasFactory, softDeletes;
//    protected $primaryKey = 'uuid';
//    public $incrementing = false;
//    protected $keyType = 'string';
    protected $fillable = [
        'user_id',
        'name',
        'uuid',
        'slug',
        'address',
        'city',
        'state',
        'zipcode',
        'country',
        'country_code',
        'latitude',
        'longitude',
        'pricing_template_id',
        'business_license',
        'authorized_signature',
        'articles_of_Incorporation',
        'national_identity_card',
        'verification_status',
        'visibility_status',
        'meta_title',
        'meta_keywords',
        'meta_description'
    ];

    /**
     * Get the user that owns the merchant.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public function countryDetails()
    {
        return $this->belongsTo(Country::class, 'country', 'name');
    }
}
