<?php

namespace Database\Factories;

use App\Models\Ternak;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Breeding>
 */
class BreedingFactory extends Factory
{
    public function definition(): array
    {
        $status = $this->faker->randomElement([
            'kosong',
            'kawin',
            'bunting',
            'nifas',
        ]);

        $tanggalKawin = null;
        $perkiraanMelahirkan = null;
        $totalMelahirkan = 0;

        switch ($status) {
            case 'kosong':
                // Belum pernah kawin
                break;

            case 'kawin':
                $tanggalKawin = Carbon::now()->subDays(rand(1, 30));
                break;

            case 'bunting':
                $tanggalKawin = Carbon::now()->subDays(rand(60, 150));
                $perkiraanMelahirkan = $tanggalKawin->copy()->addDays(150);
                $totalMelahirkan = rand(0, 3);
                break;

            case 'nifas':
                $tanggalKawin = Carbon::now()->subDays(rand(160, 220));
                $totalMelahirkan = rand(1, 5);
                break;
        }

        return [
            'status_reproduksi' => $status,
            'tanggal_kawin' => $tanggalKawin,
            'perkiraan_melahirkan' => $perkiraanMelahirkan,
            'total_melahirkan' => $totalMelahirkan,
        ];
    }

    /* =======================
     | STATES (AMAN & KONSISTEN)
     ======================= */

    public function kosong(): static
    {
        return $this->state([
            'status_reproduksi' => 'kosong',
            'tanggal_kawin' => null,
            'perkiraan_melahirkan' => null,
            'total_melahirkan' => 0,
        ]);
    }

    public function kawin(): static
    {
        return $this->state([
            'status_reproduksi' => 'kawin',
            'tanggal_kawin' => now()->subDays(rand(1, 30)),
            'perkiraan_melahirkan' => null,
            'total_melahahirkan' => 0,
        ]);
    }

    public function bunting(): static
    {
        $tanggalKawin = now()->subDays(rand(60, 150));

        return $this->state([
            'status_reproduksi' => 'bunting',
            'tanggal_kawin' => $tanggalKawin,
            'perkiraan_melahirkan' => $tanggalKawin->copy()->addDays(150),
            'total_melahirkan' => rand(0, 3),
        ]);
    }

    public function nifas(): static
    {
        return $this->state([
            'status_reproduksi' => 'nifas',
            'tanggal_kawin' => now()->subDays(rand(160, 220)),
            'perkiraan_melahirkan' => null,
            'total_melahirkan' => rand(1, 6),
        ]);
    }
}