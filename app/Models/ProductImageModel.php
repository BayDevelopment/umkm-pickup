<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImageModel extends Model
{
    protected $table = 'product_images';

    protected $fillable = [
        'product_id',
        'path',
        'is_main',
    ];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id');
    }
}
