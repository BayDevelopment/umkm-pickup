<?php

namespace App\Models;

use App\Events\OrderStatusUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class OrderModel extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'orders';

    protected $fillable = [
        'order_code',
        'user_id',
        'branch_id',
        'payment_method_id',
        'total_price',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'payment_proof',
        'payment_status',
        'status',
        'stock_restored',
        'note', // ✅ tambah ini, ada di migration tapi belum di fillable
    ];


    protected $casts = [
        'total_price' => 'integer',
        'stock_restored' => 'boolean', // ✅
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItemModel::class, 'order_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PayMethodModel::class);
    }
    public function branch()
    {
        return $this->belongsTo(BranchModel::class, 'branch_id');
    }

    protected static function booted()
    {
        // =========================
        // 🔥 GENERATE ORDER CODE
        // =========================
        static::creating(function ($order) {
            $order->order_code = 'ORD-' . now()->format('YmdHis') . rand(100, 999);
        });

        // =========================
        // 🔥 AFTER UPDATE
        // =========================
        static::updated(function (OrderModel $order) {

            // =========================
            // 🔥 RESTORE STOCK (CANCEL)
            // =========================
            if (
                $order->status === 'cancel'
                && !$order->stock_restored
            ) {

                DB::transaction(function () use ($order) {

                    $order->loadMissing('items.variant');

                    foreach ($order->items as $item) {
                        if ($item->variant) {
                            $item->variant->increment(
                                'stock',
                                $item->quantity
                            );
                        }
                    }

                    $order->updateQuietly([
                        'stock_restored' => true
                    ]);
                });
            }

            // =========================
            // 🔥 REALTIME (WEBSOCKET)
            // =========================
            if ($order->wasChanged(['status', 'payment_status'])) {

                $order->refresh();

                broadcast(new OrderStatusUpdated($order));
            }
        });
    }
}
