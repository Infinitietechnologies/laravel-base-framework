<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'tax_id',
        'rate',
    ];

    public function taxClass()
    {
        return $this->belongsTo(TaxClass::class, 'tax_id');
    }
}
