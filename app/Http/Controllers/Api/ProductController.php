<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active();

        // Сортировка по цене
        if ($request->filled('sort_by_price')) {
            $direction = $request->sort_by_price === 'desc' ? 'desc' : 'asc';
            $query->orderBy('price', $direction);
        }

        // Пагинация
        $products = $query->paginate(15);

        return ProductResource::collection($products);
    }

    public function show(Product $product)
    {
        if (!$product->is_active) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return new ProductResource($product);
    }
}