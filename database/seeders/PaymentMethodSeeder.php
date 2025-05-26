<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $paymentMethods = [
            [
                'name' => 'card',
                'display_name' => 'Банковская карта',
                'is_active' => true,
            ],
            [
                'name' => 'paypal',
                'display_name' => 'PayPal',
                'is_active' => true,
            ],
            [
                'name' => 'crypto',
                'display_name' => 'Криптовалюта',
                'is_active' => true,
            ],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::create($method);
        }
    }
}