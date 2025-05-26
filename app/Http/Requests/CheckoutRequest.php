<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method_id' => 'required|exists:payment_methods,id',
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method_id.required' => 'Payment method is required',
            'payment_method_id.exists' => 'Payment method not found',
        ];
    }
}