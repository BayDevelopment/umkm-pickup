<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItemModel extends Model
{
    use HasFactory;
    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'product_variant_id',
        'quantity',
        'price',
        'subtotal',
        'product_name',
        'variant_sku',
        'variant_attributes', // ✅ ganti color/size → JSON
        'note',
    ];


    protected $casts = [
        'price' => 'integer',
        'subtotal' => 'integer',
        'quantity' => 'integer',
        'variant_attributes' => 'array', // ✅

    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function order()
    {
        return $this->belongsTo(OrderModel::class, 'order_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariantModel::class, 'product_variant_id');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER (Optional)
    |--------------------------------------------------------------------------
    */

    // Hitung ulang subtotal otomatis
    public function calculateSubtotal()
    {
        $this->subtotal = $this->price * $this->quantity;
    }
}
