<?php

namespace App\Observers;

use App\Mail\OrderStatusUpdatedMail;
use App\Models\OrderModel;
use Illuminate\Support\Facades\Mail;

class OrderObserver
{
    /**
     * Handle the OrderModel "created" event.
     */
    public function created(OrderModel $orderModel): void
    {
        //
    }

    /**
     * Handle the OrderModel "updated" event.
     */
    public function updated(OrderModel $order)
    {
        // hanya jalan kalau benar-benar berubah
        if (!$order->wasChanged(['status', 'payment_status'])) {
            return;
        }

        // ambil data lama vs baru
        $oldStatus = $order->getOriginal('status');
        $newStatus = $order->status;

        $oldPayment = $order->getOriginal('payment_status');
        $newPayment = $order->payment_status;

        // pastikan user ada
        if (!$order->relationLoaded('user')) {
            $order->load('user');
        }

        if (!$order->user || !$order->user->email) {
            return; // safety guard
        }

        // 🚀 pakai queue (WAJIB untuk production)
        Mail::to($order->user->email)->send(
            new OrderStatusUpdatedMail(
                $order,
                $oldStatus,
                $newStatus,
                $oldPayment,
                $newPayment
            )
        );
    }

    /**
     * Handle the OrderModel "deleted" event.
     */
    public function deleted(OrderModel $orderModel): void
    {
        //
    }

    /**
     * Handle the OrderModel "restored" event.
     */
    public function restored(OrderModel $orderModel): void
    {
        //
    }

    /**
     * Handle the OrderModel "force deleted" event.
     */
    public function forceDeleted(OrderModel $orderModel): void
    {
        //
    }
}
