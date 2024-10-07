<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model
{
    use HasFactory, HasSlug, SoftDeletes;

    protected $casts = [
        'status' => ProductStatus::class
    ];

    protected $fillable = [
        'title',
        'description',
        'price',
        'image', 
        'published', 
        'image_mime', 
        'image_size',
        'storage_path', 
        'created_by', 
        'updated_by'];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

}
