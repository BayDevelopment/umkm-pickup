<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ProductModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'umkm_id', // 🔥 WAJIB TAMBAH
        'category_id',
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function category()
    {
        return $this->belongsTo(CategoryModel::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariantModel::class, 'product_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImageModel::class, 'product_id');
    }

    public function mainImage()
    {
        return $this->hasOne(ProductImageModel::class, 'product_id')
            ->where('is_main', 1);
    }

    public function branches()
    {
        return $this->belongsToMany(
            BranchModel::class,
            'product_branch',
            'product_id',
            'branch_id'
        );
    }

    public function umkm()
    {
        return $this->belongsTo(umkmModel::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getLowestPriceAttribute()
    {
        return $this->variants()->min('price') ?? 0;
    }

    public function getTotalStockAttribute()
    {
        return $this->variants()->sum('stock') ?? 0;
    }

    public function getIsInStockAttribute()
    {
        return $this->total_stock > 0;
    }

    public function getIsNewAttribute()
    {
        return $this->created_at?->diffInDays(now()) < 4;
    }

    /*
    |--------------------------------------------------------------------------
    | BOOT
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::creating(function ($product) {
            $product->slug = self::generateUniqueSlug($product->name);
        });

        static::updating(function ($product) {
            if ($product->isDirty('name')) {
                $product->slug = self::generateUniqueSlug($product->name);
            }
        });
    }

    protected static function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $count = self::where('slug', 'like', "{$slug}%")->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }
}
