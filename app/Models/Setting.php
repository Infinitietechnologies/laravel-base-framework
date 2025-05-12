<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = 'variable';  // Define the primary key

    public $incrementing = false;  // Tell Laravel it's NOT an auto-incrementing key
    protected $keyType = 'string';
    protected $fillable = ['variable','value'];
    public function getValueAttribute($value)
    {
        return json_decode($value, true);
    }
}
