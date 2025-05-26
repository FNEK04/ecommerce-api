<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_fields()
    {
        $item = OrderItem::factory()->create([
            'quantity' => 2,
            'price' => 10.5,
            'product_name' => 'Test',
        ]);
        $this->assertEquals(2, $item->quantity);
        $this->assertEquals(10.5, $item->price);
        $this->assertEquals('Test', $item->product_name);
    }

    public function test_casts()
    {
        $item = OrderItem::factory()->create(['price' => '123.45']);
        $this->assertIsNumeric($item->price);
    }

    public function test_order_relationship()
    {
        $order = Order::factory()->create();
        $item = OrderItem::factory()->create(['order_id' => $order->id]);
        $this->assertEquals($order->id, $item->order->id);
    }

    public function test_product_relationship()
    {
        $product = Product::factory()->create();
        $item = OrderItem::factory()->create(['product_id' => $product->id]);
        $this->assertEquals($product->id, $item->product->id);
    }

    public function test_total_attribute()
    {
        $item = OrderItem::factory()->create(['quantity' => 3, 'price' => 5]);
        $this->assertEquals(15, $item->total);
    }
} 