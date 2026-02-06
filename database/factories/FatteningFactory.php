<?php

namespace Database\Factories;

use App\Models\Ternak;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Fattening>
 */
class FatteningFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        
        
        // Ternak khusus kategori fattening
        $ternak = Ternak::factory()->create([
            'kategori' => 'fattening',
            'jenis_kelamin' => 'jantan',
            'status_aktif' => 'aktif',
        ]);

        $bobotAwal = $this->faker->numberBetween(15, 30);
        $bobotTerakhir = $this->faker->optional(0.7)
            ->numberBetween($bobotAwal, $bobotAwal + 20);
        
        return [
            'ternak_id' => $ternak->id,
            'bobot_awal' => $bobotAwal,
            'bobot_terakhir' => $bobotTerakhir,
            'target_bobot' => $bobotAwal + $this->faker->numberBetween(15, 30),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * State: baru mulai penggemukan
    */
    public function baru(): static
    {
        return $this->state(fn () => [
            'bobot_terakhir' => null,
        ]);
    }

     /**
     * State: sedang penggemukan
     */
    public function proses(): static
    {
        return $this->state(fn (array $attributes) => [
            'bobot_terakhir' => $this->faker->numberBetween(
                $attributes['bobot_awal'],
                $attributes['target_bobot'] - 5
            ),
        ]);
    }

    
    /**
     * State: siap panen
     */
    public function siapPanen(): static
    {
        return $this->state(fn (array $attributes) => [
            'bobot_terakhir' => $attributes['target_bobot'],
        ]);
    }
}
