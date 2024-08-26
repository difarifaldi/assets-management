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
        $user_admin = User::create([
            'username' => 'admin123',
            'nik' => '3274030904010001',
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'phone' => '081312287133',
            'address' => 'Depok 2 Tengah',
            'password' => Hash::make('password123')
        ]);
        $user_admin->assignRole('admin');
    }
}
