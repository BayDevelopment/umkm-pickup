<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'categories';
    protected $fillable = [
        'parent_id',  // ← tambahkan
        'name',
        'slug',
        'image',      // ← tambahkan
        'is_active',
    ];

    public function products()
    {
        return $this->hasMany(ProductModel::class, 'category_id');
    }

    // Relasi ke parent
    public function parent(): BelongsTo
    {
        return $this->belongsTo(CategoryModel::class, 'parent_id');
    }

    // Relasi ke children (opsional tapi berguna)
    public function children(): HasMany
    {
        return $this->hasMany(CategoryModel::class, 'parent_id');
    }
}
