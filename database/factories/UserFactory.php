<?php
// database/factories/UserFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'status' => 'active',
            'role' => 'student',
            'dob' => fake()->date(),
            'education_qualification' => fake()->randomElement(['SSC', 'HSC', 'BSc', 'MSc', 'BBA', 'MBA']), // Shorter values
            'institute' => fake()->company(),
            'experience' => fake()->randomElement(['1 year', '2 years', '3 years', 'Fresh']),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}