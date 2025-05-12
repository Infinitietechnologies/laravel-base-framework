<?php

namespace App\Models;

use App\Enums\BrandStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

//    protected $primaryKey = 'uuid';
//    public $incrementing = false;
//    protected $keyType = 'string';
    protected $fillable = [
        'uuid',
        'title',
        'slug',
        'logo',
        'banner',
        'status',
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];

    protected $casts = [
        'status' => BrandStatusEnum::class,
    ];
    public function setTitleAttribute($value): void
    {
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = Str::slug($value);


    }



}
