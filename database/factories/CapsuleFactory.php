<?php

namespace Database\Factories;

use App\Models\Capsule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Capsule>
 */
class CapsuleFactory extends Factory
{
    protected $model = Capsule::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'message' => fake()->paragraph(),
            'latitude' => fake()->latitude(36, 42), // Türkiye sınırları içinde
            'longitude' => fake()->longitude(26, 45),
            'image' => null,
            'audio' => null,
            'unlock_date' => null,
            'pin_code' => null,
            'share_code' => null,
        ];
    }

    /**
     * Kapsül PIN korumalı
     */
    public function withPin(string $pin = '1234'): static
    {
        return $this->state(fn (array $attributes) => [
            'pin_code' => Hash::make($pin),
        ]);
    }

    /**
     * Kapsül tarih kilitli
     */
    public function withUnlockDate(\DateTime|string|null $date = null): static
    {
        return $this->state(fn (array $attributes) => [
            'unlock_date' => $date ?? now()->addDays(7),
        ]);
    }

    /**
     * Kapsül paylaşım linki var
     */
    public function withShareCode(): static
    {
        return $this->state(fn (array $attributes) => [
            'share_code' => \Illuminate\Support\Str::random(12),
        ]);
    }

    /**
     * Kapsül İstanbul'da
     */
    public function inIstanbul(): static
    {
        return $this->state(fn (array $attributes) => [
            'latitude' => fake()->randomFloat(6, 40.9, 41.1),
            'longitude' => fake()->randomFloat(6, 28.8, 29.1),
        ]);
    }

    /**
     * Kapsül resim içeriyor
     */
    public function withImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'image' => 'capsules/images/test-' . fake()->uuid() . '.webp',
        ]);
    }

    /**
     * Kapsül ses içeriyor
     */
    public function withAudio(): static
    {
        return $this->state(fn (array $attributes) => [
            'audio' => 'capsules/audios/test-' . fake()->uuid() . '.mp3',
        ]);
    }
}
