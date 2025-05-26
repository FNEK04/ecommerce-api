<?php

namespace Tests\Unit;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_generates_unique_order_number()
    {
        $number1 = Order::generateOrderNumber();
        $number2 = Order::generateOrderNumber();

        $this->assertNotEquals($number1, $number2);
        $this->assertStringStartsWith('ORD-', $number1);
    }

    public function test_generates_unique_payment_url()
    {
        $url1 = Order::generatePaymentUrl();
        $url2 = Order::generatePaymentUrl();

        $this->assertNotEquals($url1, $url2);
        $this->assertStringStartsWith('payment/', $url1);
    }

    public function test_can_mark_order_as_paid()
    {
        $order = Order::factory()->create(['status' => 'pending_payment']);

        $order->markAsPaid();

        $this->assertEquals('paid', $order->status);
        $this->assertNotNull($order->paid_at);
    }

    public function test_can_mark_order_as_cancelled()
    {
        $order = Order::factory()->create(['status' => 'pending_payment']);

        $order->markAsCancelled();

        $this->assertEquals('cancelled', $order->status);
    }
}