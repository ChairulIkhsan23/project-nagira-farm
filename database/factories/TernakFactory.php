<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TernakFactory extends Factory
{
    private static $counter = 1000;

    public function definition(): array
    {
        $jenisTernak = [
            'Kambing Etawa',
            'Kambing Peranakan Etawa',
            'Kambing Boer',
            'Kambing Kacang',
            'Kambing Saanen',
            'Kambing Jawarandu',
        ];

        // Tentukan kategori
        $kategori = $this->faker->randomElement(['breeding', 'fattening']);

        // Atur jenis kelamin 
        $jenisKelamin = $kategori === 'breeding' ? 'betina' : 'jantan';

        $usia = $this->faker->numberBetween(12, 48);
        $tanggalLahir = now()->subMonths($usia);

        $counter = self::$counter++;

        return [
            'kode_ternak' => 'TRN-' . now()->format('Ymd') . '-' . $counter,
            'nama_ternak' => $this->faker->firstName(),
            'jenis_ternak' => $this->faker->randomElement($jenisTernak),

            'kategori' => $kategori,
            'jenis_kelamin' => $jenisKelamin,

            'tanggal_lahir' => $tanggalLahir->format('Y-m-d'),
            'foto' => null,
            'status_kesehatan' => $this->faker->randomElement(['sehat', 'perawatan']),
            'status_aktif' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /* 
    ** State Umum
    */

    public function sehat(): static
    {
        return $this->state(fn () => [
            'status_kesehatan' => 'sehat',
        ]);
    }

    public function perawatan(): static
    {
        return $this->state(fn () => [
            'status_kesehatan' => 'perawatan',
        ]);
    }

    public function muda(): static
    {
        return $this->state(fn () => [
            'tanggal_lahir' => now()->subMonths(rand(12, 24))->format('Y-m-d'),
        ]);
    }

    public function dewasa(): static
    {
        return $this->state(fn () => [
            'tanggal_lahir' => now()->subYears(rand(2, 4))->format('Y-m-d'),
        ]);
    }

    public function tua(): static
    {
        return $this->state(fn () => [
            'tanggal_lahir' => now()->subYears(rand(5, 8))->format('Y-m-d'),
        ]);
    }

    /* 
    ** State Spesifik
    */

    public function breeding(): static
    {
        return $this->state(fn () => [
            'kategori' => 'breeding',
            'jenis_kelamin' => 'betina',
        ]);
    }

    public function fattening(): static
    {
        return $this->state(fn () => [
            'kategori' => 'fattening',
            'jenis_kelamin' => 'jantan',
            'status_kesehatan' => 'sehat',
        ]);
    }
}
