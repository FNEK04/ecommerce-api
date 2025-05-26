<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Http\Resources\CartResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = auth()->user()->getOrCreateCart();
        $cart->load(['items.product']);

        return (new CartResource($cart))->response()->setStatusCode(200);
    }

    public function add(AddToCartRequest $request): JsonResponse
    {
        $product = Product::findOrFail($request->product_id);
        
        if (!$product->is_active) {
            return response()->json(['message' => 'Product is not available'], 400);
        }

        if ($product->stock < $request->quantity) {
            return response()->json(['message' => 'Not enough stock'], 400);
        }

        $cart = auth()->user()->getOrCreateCart();
        
        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $request->quantity;
            
            if ($product->stock < $newQuantity) {
                return response()->json(['message' => 'Not enough stock'], 400);
            }
            
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->price,
            ]);
        }

        $cart->load(['items.product']);

        return response()->json([
            'message' => 'Product added to cart',
            'cart' => new CartResource($cart),
        ]);
    }

    public function remove(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $cart = auth()->user()->cart;

        if (!$cart) {
            return response()->json([
                'message' => 'Product removed from cart',
                'cart' => null,
            ], 200);
        }

        $cartItem = $cart->items()->where('product_id', $request->product_id)->first();

        if (!$cartItem) {
            return response()->json([
                'message' => 'Product removed from cart',
                'cart' => new CartResource($cart),
            ], 200);
        }

        $cartItem->delete();

        $cart->load(['items.product']);

        return response()->json([
            'message' => 'Product removed from cart',
            'cart' => new CartResource($cart),
        ]);
    }
}