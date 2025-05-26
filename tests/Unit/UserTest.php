<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_fields()
    {
        $user = User::factory()->create([
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'secret',
        ]);
        $this->assertEquals('Test', $user->name);
        $this->assertEquals('test@example.com', $user->email);
    }

    public function test_hidden_fields()
    {
        $user = User::factory()->create(['password' => 'secret']);
        $this->assertArrayNotHasKey('password', $user->toArray());
    }

    public function test_cart_relationship()
    {
        $user = User::factory()->create();
        $this->assertTrue(method_exists($user, 'cart'));
    }

    public function test_orders_relationship()
    {
        $user = User::factory()->create();
        $this->assertTrue(method_exists($user, 'orders'));
    }

    public function test_get_or_create_cart()
    {
        $user = User::factory()->create();
        $cart = $user->getOrCreateCart();
        $this->assertEquals($user->id, $cart->user_id);
    }
} 