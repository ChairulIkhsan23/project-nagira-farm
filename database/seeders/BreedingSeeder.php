<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ternak;
use App\Models\Breeding;

class BreedingSeeder extends Seeder
{
    public function run(): void
    {
        $ternaks = Ternak::query()
            ->where('kategori', 'breeding')
            ->where('jenis_kelamin', 'betina')
            ->where('status_aktif', 'aktif')
            ->doesntHave('breeding')
            ->take(10)
            ->get();

        foreach ($ternaks as $ternak) {
            Breeding::factory()
                ->for($ternak)
                ->create();
        }
    }
}
