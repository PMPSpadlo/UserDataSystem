<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'], // Unikalny identyfikator
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'), // Bezpieczne hasło
                'is_admin' => true,
            ]
        );
    }
}
