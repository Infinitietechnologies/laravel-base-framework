<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductCondition extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'category_id',
        'title',
        'slug',
        'alignment',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Automatically generate UUID
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
            $model->slug = createUniqueSlug($model->title, "productCondition");
        });

        static::updating(function ($model) {
            if ($model->isDirty('title')) {
                $model->slug = createUniqueSlug($model->title, "productCondition", $model->id);
            }
        });
    }

    // Getters
    public function getUuid()
    {
        return $this->uuid;
    }

    public function getCategoryId()
    {
        return $this->category_id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getAlignment()
    {
        return $this->alignment;
    }

    // Setters
    public function setCategoryId($value)
    {
        $this->category_id = $value;
    }

    public function setTitle($value)
    {
        $this->title = $value;
    }

    public function setSlug($value)
    {
        $this->slug = $value;
    }

    public function setAlignment($value)
    {
        $this->alignment = $value;
    }

    // Enum method for alignment field
    public static function getAlignments()
    {
        return ['strip'];
    }
}
