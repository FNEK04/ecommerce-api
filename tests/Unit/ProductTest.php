<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_fields()
    {
        $product = Product::factory()->create([
            'name' => 'Test',
            'description' => 'Desc',
            'price' => 100.50,
            'stock' => 10,
            'image_url' => 'img.jpg',
            'is_active' => true,
        ]);
        $this->assertEquals('Test', $product->name);
        $this->assertEquals('Desc', $product->description);
        $this->assertEquals(100.50, $product->price);
        $this->assertEquals(10, $product->stock);
        $this->assertEquals('img.jpg', $product->image_url);
        $this->assertTrue($product->is_active);
    }

    public function test_casts()
    {
        $product = Product::factory()->create([
            'price' => '123.45',
            'is_active' => 1,
        ]);
        $this->assertIsNumeric($product->price);
        $this->assertIsBool($product->is_active);
    }

    public function test_scope_active()
    {
        Product::factory()->create(['is_active' => true]);
        Product::factory()->create(['is_active' => false]);
        $this->assertCount(1, Product::active()->get());
    }

    public function test_scope_in_stock()
    {
        Product::factory()->create(['stock' => 5]);
        Product::factory()->create(['stock' => 0]);
        $this->assertCount(1, Product::inStock()->get());
    }

    public function test_cart_items_relationship()
    {
        $product = Product::factory()->create();
        $this->assertTrue(method_exists($product, 'cartItems'));
    }

    public function test_order_items_relationship()
    {
        $product = Product::factory()->create();
        $this->assertTrue(method_exists($product, 'orderItems'));
    }
} 