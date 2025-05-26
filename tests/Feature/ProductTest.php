<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_products()
    {
        Product::factory()->count(5)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'price', 'stock']
                ]
            ]);
    }

    public function test_can_show_single_product()
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name,
                ]
            ]);
    }

    public function test_can_sort_products_by_price()
    {
        Product::factory()->create(['price' => 100]);
        Product::factory()->create(['price' => 50]);
        Product::factory()->create(['price' => 150]);

        $response = $this->getJson('/api/products?sort_by_price=asc');

        $response->assertStatus(200);
        
        $products = $response->json('data');
        $this->assertEquals(50, $products[0]['price']);
        $this->assertEquals(150, $products[2]['price']);
    }
}