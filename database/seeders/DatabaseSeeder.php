<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Sampah',
            'email' => 'admin@recyclehub.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Renata',
            'email' => 'Renata@recyclehub.test',
            'password' => Hash::make('password123'),      
            'role' => 'nasabah',
        ]);

        User::create([
            'name' => 'Auliya',
            'email' => 'Auliya@recyclehub.test',
            'password' => Hash::make('password123'),
            'role' => 'nasabah',
        ]);
    }
}