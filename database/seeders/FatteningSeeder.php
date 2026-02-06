<?php

namespace Database\Seeders;

use App\Models\Fattening;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FatteningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Fattening::factory()->count(10)->create();
    }
}
