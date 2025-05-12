<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'title', 'country', 'country_code'
    ];

    public function taxRates()
    {
        return $this->hasMany(TaxRate::class, 'tax_id');
    }
}
