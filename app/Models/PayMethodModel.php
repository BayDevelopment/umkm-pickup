<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayMethodModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payment_methods';

    protected $fillable = [
        'umkm_id',        // 🔥 tambah ini
        'name',           // "QRIS", "Transfer BCA", "Cash"
        'bank_name',      // "BCA", "Mandiri", null kalau cash/qris
        'account_number', // no rek / no HP qris
        'account_name',   // nama pemilik
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function umkm()
    {
        return $this->belongsTo(umkmModel::class, 'umkm_id');
    }
    public function orders()
    {
        return $this->hasMany(OrderModel::class);
    }
}
