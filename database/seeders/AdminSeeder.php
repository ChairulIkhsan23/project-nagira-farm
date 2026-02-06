<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
 public function run(): void
    {
        // Super Admin
        User::updateOrCreate(
            ['email' => 'admin@nagirafarm.com'],
            [
                'nama' => 'Super Admin',
                'username' => 'nagirafarm',
                'nomor_hp' => '082121124177',
                'password' => bcrypt('nagira123'),
                'role' => 'admin'
            ]
        );

        // Chairul Ikhsan
        User::updateOrCreate(
            ['email' => 'chairulikhsan2121@gmail.com'],
            [
                'nama' => 'Chairul Ikhsan',
                'username' => 'chairulikhsan21',
                'nomor_hp' => '082121124177',
                'password' => bcrypt('ikhsan123'),
                'role' => 'admin'
            ]
        );
    }
}
