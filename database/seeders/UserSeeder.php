<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'admin@infinitylead.io',
            'password' => Hash::make('admin@infinitylead.io'),
            'role' => User::ROLE_ADMIN,
            'status'=>USER::STATUS_ACTIVE,
        ]);
        //
    }
}
