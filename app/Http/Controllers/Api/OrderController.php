<?php
// app/Http/Controllers/Api/OrderController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->orders();

        // Фильтрация по статусу
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Сортировка по дате
        $sortDirection = $request->sort_date === 'asc' ? 'asc' : 'desc';
        $query->orderBy('created_at', $sortDirection);

        $orders = $query->with(['items.product', 'paymentMethod'])->paginate(10);

        return OrderResource::collection($orders);
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->load(['items.product', 'paymentMethod']);

        return new OrderResource($order);
    }

    public function checkout(CheckoutRequest $request): JsonResponse
    {
        $cart = auth()->user()->cart;

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);

        if (!$paymentMethod->is_active) {
            return response()->json(['message' => 'Payment method is not available'], 400);
        }

        // Проверяем наличие товаров
        foreach ($cart->items as $item) {
            if ($item->product->stock < $item->quantity) {
                return response()->json([
                    'message' => "Not enough stock for product: {$item->product->name}"
                ], 400);
            }
        }

        DB::beginTransaction();

        try {
            // Создаем заказ
            $order = Order::create([
                'user_id' => auth()->id(),
                'payment_method_id' => $paymentMethod->id,
                'order_number' => Order::generateOrderNumber(),
                'total_amount' => $cart->total_amount,
                'payment_url' => Order::generatePaymentUrl(),
                'expires_at' => now()->addMinutes(2),
            ]);

            // Копируем товары из корзины в заказ
            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'product_name' => $item->product->name,
                ]);

                // Уменьшаем количество товара на складе
                $item->product->decrement('stock', $item->quantity);
            }

            // Удаляем корзину
            $cart->delete();

            DB::commit();

            $order->load(['items', 'paymentMethod']);

            return response()->json([
                'message' => 'Order created successfully',
                'order' => new OrderResource($order),
                'payment_url' => url($order->payment_url),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create order'], 500);
        }
    }

    public function markAsPaid(Order $order): JsonResponse
    {
        if ($order->status !== 'pending_payment') {
            return response()->json(['message' => 'Order cannot be paid'], 400);
        }

        if ($order->expires_at < now()) {
            $order->markAsCancelled();
            return response()->json(['message' => 'Order has expired'], 400);
        }

        $order->markAsPaid();

        return response()->json([
            'message' => 'Order paid successfully',
            'order' => new OrderResource($order->load(['items', 'paymentMethod'])),
        ]);
    }

    public function paymentCallback(Request $request, $paymentUrl): JsonResponse
    {
        $order = Order::where('payment_url', "payment/{$paymentUrl}")->first();

        if (!$order) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        return $this->markAsPaid($order);
    }
}