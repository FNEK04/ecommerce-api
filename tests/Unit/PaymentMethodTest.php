<?php

namespace Tests\Unit;

use App\Models\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_fields()
    {
        $method = PaymentMethod::factory()->create([
            'name' => 'card',
            'display_name' => 'Card',
            'is_active' => true,
        ]);
        $this->assertEquals('card', $method->name);
        $this->assertEquals('Card', $method->display_name);
        $this->assertTrue($method->is_active);
    }

    public function test_casts()
    {
        $method = PaymentMethod::factory()->create(['is_active' => 1]);
        $this->assertIsBool($method->is_active);
    }

    public function test_scope_active()
    {
        PaymentMethod::factory()->create(['is_active' => true]);
        PaymentMethod::factory()->create(['is_active' => false]);
        $this->assertCount(1, PaymentMethod::active()->get());
    }

    public function test_orders_relationship()
    {
        $method = PaymentMethod::factory()->create();
        $this->assertTrue(method_exists($method, 'orders'));
    }
} 