<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\Category\CategoryStatusEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

//    protected $primaryKey = 'uuid';
    protected $fillable = [
        'uuid',
        'parent_id',
        'title',
        'slug',
        'description',
        'image',
        'banner',
        'status',
        'requires_approval',
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'status' => CategoryStatusEnum::class,
        'requires_approval' => 'boolean',
    ];

    public function productConditions()
    {
        return $this->hasMany(ProductCondition::class);
    }
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Category $model) {
            $model->setAttribute('uuid', (string) Str::uuid());
        });
    }

    public function setTitleAttribute($value): void
    {
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function getDescriptionAttribute($value): string
    {
        return nl2br($value);
    }

    public function getMetaTitleAttribute($value): ?string
    {
        return $value ?: $this->attributes['title'];
    }

    public function getMetaDescriptionAttribute($value): ?string
    {
        return $value ?: Str::limit($this->attributes['description'], 160);
    }

    public function getParentId(): ?int
    {
        return $this->attributes['parent_id'];
    }

    public function setParentId(?int $parent_id): void
    {
        $this->attributes['parent_id'] = $parent_id;
    }

    public function getTitle(): string
    {
        return $this->attributes['title'];
    }

    public function setTitle(string $title): void
    {
        $this->attributes['title'] = $title;
    }

    public function getSlug(): string
    {
        return $this->attributes['slug'];
    }

    public function setSlug(string $slug): void
    {
        $this->attributes['slug'] = $slug;
    }

    public function getDescription(): ?string
    {
        return $this->attributes['description'];
    }

    public function setDescription(?string $description): void
    {
        $this->attributes['description'] = $description;
    }

    public function getImage(): string
    {
        return $this->attributes['image'];
    }

    public function setImage(string $image): void
    {
        $this->attributes['image'] = $image;
    }

    public function getBanner(): ?string
    {
        return $this->attributes['banner'];
    }

    public function setBanner(?string $banner): void
    {
        $this->attributes['banner'] = $banner;
    }

    public function getStatus(): CategoryStatusEnum
    {
        return $this->attributes['status'];
    }

    public function setStatus(CategoryStatusEnum $status): void
    {
        $this->attributes['status'] = $status;
    }

    public function getRequiresApproval(): bool
    {
        return $this->attributes['requires_approval'];
    }

    public function setRequiresApproval(bool $requires_approval): void
    {
        $this->attributes['requires_approval'] = $requires_approval;
    }

    public function getMetaTitle(): ?string
    {
        return $this->attributes['meta_title'];
    }

    public function setMetaTitle(?string $meta_title): void
    {
        $this->attributes['meta_title'] = $meta_title;
    }

    public function getMetaKeywords(): ?string
    {
        return $this->attributes['meta_keywords'];
    }

    public function setMetaKeywords(?string $meta_keywords): void
    {
        $this->attributes['meta_keywords'] = $meta_keywords;
    }

    public function getMetaDescription(): ?string
    {
        return $this->attributes['meta_description'];
    }

    public function setMetaDescription(?string $meta_description): void
    {
        $this->attributes['meta_description'] = $meta_description;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
