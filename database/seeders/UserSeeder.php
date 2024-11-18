<?php

namespace Database\Seeders;

use App\Models\master\User;
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
            'nama' => 'admin',
            'email' => 'admin@gmail.com',
            'noHP' => '081312287133',
            'alamat' => 'Depok 2 Tengah',
            'password' => Hash::make('password123')
        ]);
        $user_admin->assignRole('admin');
    }
}
