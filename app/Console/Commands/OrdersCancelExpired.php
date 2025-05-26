<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class OrdersCancelExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel orders that have expired (status pending_payment and expires_at has passed)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to cancel expired orders...');

        $expiredOrders = Order::where('status', Order::STATUS_PENDING_PAYMENT)
                                ->where('expires_at', '<', Carbon::now())
                                ->get();

        if ($expiredOrders->isEmpty()) {
            $this->info('No expired orders found to cancel.');
            return 0;
        }

        foreach ($expiredOrders as $order) {
            $order->markAsCancelled();
            $this->info("Order #{$order->order_number} (ID: {$order->id}) has been cancelled.");
        }

        $this->info('Finished cancelling expired orders. Cancelled: ' . $expiredOrders->count() . ' orders.');
        return 0;
    }
}
