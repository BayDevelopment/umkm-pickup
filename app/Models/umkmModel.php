<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class umkmModel extends Model
{
    use SoftDeletes;

    protected $table = 'umkms';

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'city',
        'ktp_number',
        'ktp_image',
        'selfie_image',
        'verification_status',
        'verification_note',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // 🔹 UMKM punya banyak user (owner bisa 1 atau lebih)
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // 🔹 UMKM punya banyak produk
    public function products()
    {
        return $this->hasMany(ProductModel::class, 'umkm_id');
    }

    // 🔹 UMKM punya banyak order (optional nanti)
    public function orders()
    {
        return $this->hasMany(OrderItemModel::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS (OPTIONAL BAGUS)
    |--------------------------------------------------------------------------
    */

    // Status label (biar enak di UI)
    public function getStatusLabelAttribute()
    {
        return match ($this->verification_status) {
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Unknown',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES (OPTIONAL ADVANCED)
    |--------------------------------------------------------------------------
    */

    public function scopeApproved($query)
    {
        return $query->where('verification_status', 'approved');
    }
}
