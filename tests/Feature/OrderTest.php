<?php

namespace Tests\Feature;

use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Sanctum::actingAs(User::factory()->create());
    }

    public function test_can_checkout_cart()
    {
        $product = Product::factory()->create(['stock' => 10]);
        $paymentMethod = PaymentMethod::factory()->create();

        // Добавляем товар в корзину
        $this->postJson('/api/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        // Оформляем заказ
        $response = $this->postJson('/api/checkout', [
            'payment_method_id' => $paymentMethod->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'order' => ['id', 'order_number', 'status'],
                'payment_url'
            ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => auth()->id(),
            'status' => 'pending_payment',
        ]);

        // Проверяем, что корзина удалена
        $this->assertDatabaseMissing('carts', [
            'user_id' => auth()->id(),
        ]);
    }

    public function test_cannot_checkout_empty_cart()
    {
        $paymentMethod = PaymentMethod::factory()->create();

        $response = $this->postJson('/api/checkout', [
            'payment_method_id' => $paymentMethod->id,
        ]);

        $response->assertStatus(400)
            ->assertJson(['message' => 'Cart is empty']);
    }

    public function test_can_mark_order_as_paid()
    {
        $order = Order::factory()->create([
            'user_id' => auth()->id(),
            'status' => 'pending_payment',
            'expires_at' => now()->addMinutes(5), // не просрочен
        ]);

        $response = $this->postJson("/api/orders/{$order->id}/paid");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Order paid successfully']);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'paid',
        ]);
    }

    public function test_cannot_pay_expired_order()
    {
        $order = Order::factory()->create([
            'user_id' => auth()->id(),
            'status' => 'pending_payment',
            'expires_at' => now()->subMinutes(5), // просрочен
        ]);

        $response = $this->postJson("/api/orders/{$order->id}/paid");

        $response->assertStatus(400)
            ->assertJson(['message' => 'Order has expired']);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_can_list_user_orders()
    {
        Order::factory()->count(3)->create(['user_id' => auth()->id()]);

        $response = $this->getJson('/api/orders');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'order_number', 'status', 'total_amount']
                ]
            ]);
    }
}