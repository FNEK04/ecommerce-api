<?php
// app/Http/Resources/OrderResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'total_amount' => $this->total_amount,
            'payment_method' => new PaymentMethodResource($this->whenLoaded('paymentMethod')),
            'payment_url' => $this->when(
                $this->status === 'pending_payment',
                url($this->payment_url)
            ),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'expires_at' => $this->expires_at,
            'paid_at' => $this->paid_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}