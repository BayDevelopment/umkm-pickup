<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class OrderModel extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'orders';
    protected $fillable = [
        'user_id',
        'branch_id', // 🔥 tambahin ini
        'payment_method_id',
        'total_price',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'payment_proof',
        'payment_status',
        'status',
    ];

    protected $casts = [
        'total_price' => 'integer',
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

    // gabungan dengan websocket pusher
    protected static function booted()
    {
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

            // hanya broadcast kalau status berubah
            if ($order->wasChanged(['status', 'payment_status'])) {

                $order->refresh();

                // broadcast(new \App\Events\OrderStatusUpdated($order))->toOthers();
                broadcast(new \App\Events\OrderStatusUpdated($order));
            }
        });
    }
}
