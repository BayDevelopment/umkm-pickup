<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItemModel extends Model
{
    protected $table = 'cart_items';
    protected $fillable = [
        'cart_id',
        'variant_id',
        'qty',
        'price'
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariantModel::class, 'variant_id');
    }
    public function cart()
    {
        return $this->belongsTo(CartModel::class, 'cart_id');
    }
    public function product()
    {
        return $this->variant->product();
    }
}
