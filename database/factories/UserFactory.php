<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama'        => fake()->name(),
            'username'    => fake()->unique()->userName(),
            'password'    => static::$password ??= Hash::make('password'),
            'role'        => 'kelompok',
            'id_cdk'      => null,
            'id_kelompok' => null,
            'created_at'  => now(),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role'        => 'admin',
            'id_cdk'      => null,
            'id_kelompok' => null,
        ]);
    }

    public function adminCdk(?int $idCdk = null): static
    {
        return $this->state(fn (array $attributes) => [
            'role'        => 'admin_cdk',
            'id_cdk'      => $idCdk ?? 1,
            'id_kelompok' => null,
        ]);
    }

    public function pendamping(?int $idCdk = null): static
    {
        return $this->state(fn (array $attributes) => [
            'role'        => 'pendamping',
            'id_cdk'      => $idCdk ?? 1,
            'id_kelompok' => null,
        ]);
    }

    public function kelompok(?int $idKelompok = null): static
    {
        return $this->state(fn (array $attributes) => [
            'role'        => 'kelompok',
            'id_cdk'      => null,
            'id_kelompok' => $idKelompok ?? 1,
        ]);
    }
}
