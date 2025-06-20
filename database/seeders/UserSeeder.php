<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // Ganti dengan password yang kuat di produksi
            'role' => 'admin',
            'email_verified_at' => now(), // Verifikasi email secara otomatis untuk seeder
        ]);

        // User PPIC
        User::create([
            'name' => 'PPIC User',
            'email' => 'ppic@example.com',
            'password' => Hash::make('password'),
            'role' => 'ppic',
            'email_verified_at' => now(),
        ]);

        // User Supplier
        User::create([
            'name' => 'Supplier User',
            'email' => 'supplier@example.com',
            'password' => Hash::make('password'),
            'role' => 'supplier',
            'email_verified_at' => now(),
        ]);

        // User Aii (jika memang role khusus)
        // User::create([
        //     'name' => 'Aii User',
        //     'email' => 'aii@example.com',
        //     'password' => Hash::make('password'),
        //     'role' => 'aii',
        //     'email_verified_at' => now(),
        // ]);
    }
}