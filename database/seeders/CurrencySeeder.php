<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('currencies')->truncate();
        Currency::create([
            'name' => 'US Dollar',
            'code' => 'USD',
            'format' => '${PRICE}',
        ]);
    }
    //php artisan db:seed --class=CurrencySeeder
}
