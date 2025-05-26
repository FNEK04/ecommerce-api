<?php

namespace Tests\Unit;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_fields()
    {
        $user = \App\Models\User::factory()->create();
        $cart = \App\Models\Cart::factory()->create(['user_id' => $user->id]);
        $this->assertEquals($user->id, $cart->user_id);
    }

    public function test_items_relationship()
    {
        $cart = Cart::factory()->create();
        $item = CartItem::factory()->create(['cart_id' => $cart->id]);
        $this->assertTrue($cart->items->contains($item));
    }

    public function test_total_amount_and_items()
    {
        $cart = Cart::factory()->create();
        CartItem::factory()->create(['cart_id' => $cart->id, 'quantity' => 2, 'price' => 10]);
        CartItem::factory()->create(['cart_id' => $cart->id, 'quantity' => 1, 'price' => 20]);
        $this->assertEquals(40, $cart->total_amount);
        $this->assertEquals(3, $cart->total_items);
    }
} 