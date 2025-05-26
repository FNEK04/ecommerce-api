<?php

namespace Tests\Unit;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_fields()
    {
        $item = CartItem::factory()->create([
            'quantity' => 2,
            'price' => 10.5,
        ]);
        $this->assertEquals(2, $item->quantity);
        $this->assertEquals(10.5, $item->price);
    }

    public function test_casts()
    {
        $item = CartItem::factory()->create(['price' => '123.45']);
        $this->assertIsNumeric($item->price);
    }

    public function test_cart_relationship()
    {
        $cart = Cart::factory()->create();
        $item = CartItem::factory()->create(['cart_id' => $cart->id]);
        $this->assertEquals($cart->id, $item->cart->id);
    }

    public function test_product_relationship()
    {
        $product = Product::factory()->create();
        $item = CartItem::factory()->create(['product_id' => $product->id]);
        $this->assertEquals($product->id, $item->product->id);
    }

    public function test_total_attribute()
    {
        $item = CartItem::factory()->create(['quantity' => 3, 'price' => 5]);
        $this->assertEquals(15, $item->total);
    }
} 