<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariantModel extends Model
{
    use SoftDeletes;

    protected $table = 'product_variants';

    protected $fillable = [
        'product_id',
        'branch_id',
        'sku',
        'name', // contoh: "Size L - Hitam" / "Ayam + Es Teh"
        'price',
        'stock',
        'attributes', // JSON (size, color, topping, dll)
    ];

    protected $casts = [
        'attributes' => 'array',
    ];
    protected $with = ['branch']; // 🔥 WAJIB TAMBAH INI
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id');
    }

    public function branch()
    {
        return $this->belongsTo(BranchModel::class, 'branch_id'); // pastikan foreign key-nya benar
    }

    public function cartItems()
    {
        return $this->hasMany(CartItemModel::class, 'variant_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItemModel::class, 'variant_id');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR (OPTIONAL)
    |--------------------------------------------------------------------------
    */

    public function getFormattedAttributesAttribute()
    {
        if (!$this->attributes) return null;

        return collect($this->attributes)
            ->map(fn($v, $k) => ucfirst($k) . ': ' . $v)
            ->implode(', ');
    }
}
