<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // ubah sesuai kebutuhan
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Karyawan Contoh',
            'email' => 'karyawan@example.com',
            'password' => Hash::make('password'),
            'role' => 'karyawan',
        ]);
    }
}
