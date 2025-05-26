<?php

namespace App\Console\Commands;

use App\Jobs\CancelExpiredOrders;
use Illuminate\Console\Command;

class CheckExpiredOrders extends Command
{
    protected $signature = 'orders:check-expired';
    protected $description = 'Check and cancel expired orders';

    public function handle()
    {
        $this->info('Checking for expired orders...');
        
        CancelExpiredOrders::dispatch();
        
        $this->info('Job dispatched to check expired orders');
    }
}