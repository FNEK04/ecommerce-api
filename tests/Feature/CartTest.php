<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        \DB::statement('PRAGMA foreign_keys=ON');
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_can_add_product_to_cart()
    {
        $product = Product::factory()->create(['stock' => 10]);

        $response = $this->actingAs($this->user)->postJson('/api/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Product added to cart']);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_cannot_add_more_than_stock()
    {
        $product = Product::factory()->create(['stock' => 5]);

        $response = $this->actingAs($this->user)->postJson('/api/cart/add', [
            'product_id' => $product->id,
            'quantity' => 10,
        ]);

        $response->assertStatus(400)
            ->assertJson(['message' => 'Not enough stock']);
    }

    public function test_can_remove_product_from_cart()
    {
        $product = Product::factory()->create();
        
        // Добавляем товар в корзину
        $this->actingAs($this->user)->postJson('/api/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        // Удаляем товар из корзины
        $response = $this->actingAs($this->user)->deleteJson('/api/cart/remove', [
            'product_id' => $product->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Product removed from cart']);

        $this->assertDatabaseMissing('cart_items', [
            'product_id' => $product->id,
        ]);
    }

    public function test_can_view_cart()
    {
        $product = Product::factory()->create();
        
        $this->actingAs($this->user)->postJson('/api/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/cart');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'items' => [
                        '*' => ['product', 'quantity', 'price']
                    ],
                    'total_amount'
                ]
            ]);
    }
}