<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchModel extends Model
{
    use HasFactory;
    // use SoftDeletes; // uncomment kalau mau support soft delete
    protected $table = 'branches';
    protected $fillable = [
        'name',
        'slug',
        'address',
        'city',
        'district',       // kecamatan
        'subdistrict',    // kelurahan
        'postal_code',
        'latitude',
        'longitude',
        'phone',
        'opening_time',
        'closing_time',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'opening_time'  => 'datetime:H:i',
        'closing_time'  => 'datetime:H:i',
        'latitude'      => 'float',
        'longitude'     => 'float',
    ];

    public function umkm()
    {
        return $this->belongsTo(umkmModel::class, 'umkm_id');
    }

    // Relasi ke orders (satu cabang bisa punya banyak order)
    public function orders()
    {
        return $this->hasMany(OrderModel::class);
    }

    // Relasi ke carts (satu cabang banyak cart aktif)
    public function carts()
    {
        return $this->hasMany(CartModel::class);
    }

    public function productVariants()
    {
        return $this->hasMany(
            ProductVariantModel::class,
            'branch_id',
            'id'
        );
    }


    // Optional: kalau nanti user punya default branch
    public function users()
    {
        return $this->hasMany(User::class, 'default_branch_id');
    }

    // Scope: hanya cabang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper: cek apakah cabang sedang buka (bisa dipakai di frontend)
    public function isOpen(): bool
    {
        $now = now();
        $open  = $this->opening_time ? $now->format('H:i') : null;
        $close = $this->closing_time ? $now->format('H:i') : null;

        if (!$open || !$close) return true; // kalau jam tidak diisi, anggap selalu buka

        return $now->between($this->opening_time, $this->closing_time);
    }

    // Optional: accessor untuk full address
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->subdistrict,
            $this->district,
            $this->city,
            $this->postal_code,
        ]);

        return implode(', ', $parts);
    }
}
