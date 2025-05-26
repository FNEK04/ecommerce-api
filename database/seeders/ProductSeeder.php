<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Смартфон iPhone 15',
                'description' => 'Новейший смартфон от Apple с улучшенной камерой',
                'price' => 89999.00,
                'stock' => 50,
                'image_url' => 'https://example.com/iphone15.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Ноутбук MacBook Pro',
                'description' => 'Профессиональный ноутбук для работы и творчества',
                'price' => 159999.00,
                'stock' => 25,
                'image_url' => 'https://example.com/macbook.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Наушники AirPods Pro',
                'description' => 'Беспроводные наушники с шумоподавлением',
                'price' => 24999.00,
                'stock' => 100,
                'image_url' => 'https://example.com/airpods.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Планшет iPad Air',
                'description' => 'Легкий и мощный планшет для работы и развлечений',
                'price' => 54999.00,
                'stock' => 35,
                'image_url' => 'https://example.com/ipad.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Умные часы Apple Watch',
                'description' => 'Спортивные умные часы с GPS',
                'price' => 34999.00,
                'stock' => 75,
                'image_url' => 'https://example.com/watch.jpg',
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
