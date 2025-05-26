<?php

namespace Tests\Unit;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_fields()
    {
        $order = Order::factory()->create([
            'status' => 'pending_payment',
            'total_amount' => 100.50,
        ]);
        $this->assertEquals('pending_payment', $order->status);
        $this->assertEquals(100.50, $order->total_amount);
    }

    public function test_casts()
    {
        $order = Order::factory()->create([
            'total_amount' => '123.45',
        ]);
        $this->assertIsNumeric($order->total_amount);
    }

    public function test_scope_pending_payment()
    {
        Order::factory()->create(['status' => 'pending_payment']);
        Order::factory()->create(['status' => 'paid']);
        $this->assertCount(1, Order::pendingPayment()->get());
    }

    public function test_scope_expired()
    {
        Order::factory()->create(['expires_at' => now()->subMinutes(3)]);
        Order::factory()->create(['expires_at' => now()->addMinutes(3)]);
        $this->assertCount(1, Order::expired()->get());
    }

    public function test_mark_as_paid()
    {
        $order = Order::factory()->create(['status' => 'pending_payment']);
        $order->markAsPaid();
        $this->assertEquals('paid', $order->status);
        $this->assertNotNull($order->paid_at);
    }

    public function test_mark_as_cancelled()
    {
        $order = Order::factory()->create(['status' => 'pending_payment']);
        $order->markAsCancelled();
        $this->assertEquals('cancelled', $order->status);
    }

    public function test_generate_order_number()
    {
        $number = Order::generateOrderNumber();
        $this->assertStringStartsWith('ORD-', $number);
    }

    public function test_generate_payment_url()
    {
        $url = Order::generatePaymentUrl();
        $this->assertStringStartsWith('payment/', $url);
    }
} 