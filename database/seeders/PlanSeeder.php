<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('plans')->truncate();
        Plan::create([
            'name'=>'Free',
            'description'=>'All the basics for businesses or individual to get started with email marketing',
            'price'=>0,
            'currency_id'=>1,
            'credits'=>1000,
        ]);
        Plan::create([
            'name' => 'Standard',
            'description' => 'Powerful statistics & insight report for maximized sales & marketing performance',
            'price' => 100,
            'currency_id' => 1,
            'credits' =>5000,
        ]);

    }
}
