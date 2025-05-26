<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CancelExpiredOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $expiredOrders = Order::pendingPayment()
            ->expired()
            ->get();

        foreach ($expiredOrders as $order) {
            $order->markAsCancelled();
            
            // Возвращаем товары на склад
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            Log::info("Order {$order->order_number} has been cancelled due to expiration");
        }

        if ($expiredOrders->count() > 0) {
            Log::info("Cancelled {$expiredOrders->count()} expired orders");
        }
    }
}