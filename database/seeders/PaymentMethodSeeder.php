<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payment_methods')->truncate();
        PaymentMethod::create([
            'name' => 'Offline',
            'slug' => 'offline',
            'description' => 'Receive payments outside of the application',
            'icon' => '/assets/img/bank.png',
            'status'=> PaymentMethod::STATUS_INACTIVE,
        ]);
        PaymentMethod::create([
            'name' => 'Stripe',
            'slug' => 'stripe',
            'description' => 'Receive payments from Credit / Debit card to your Stripe account',
            'icon' => '/assets/img/stripe.png',
            'status'=> PaymentMethod::STATUS_INACTIVE,
        ]);

    }
    //php artisan db:seed --class=PaymentMethodSeeder
}
