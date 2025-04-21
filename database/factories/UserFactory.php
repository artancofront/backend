<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as FakerFactory;

class UserFactory extends Factory
{
    public function definition(): array
    {
        $faker = FakerFactory::create('fa_IR'); // Persian locale

        return [
            'phone' => '09' . $this->faker->unique()->numerify('#########'),
            'email' => $this->faker->unique()->safeEmail,
            'name' => $faker->name,
            'phone_verified_at' => now(),
            'password' => Hash::make('password'), // Default password
            'role_id' => Role::inRandomOrder()->value('id'), // Optional, assumes roles exist
            'remember_token' => \Str::random(10),
        ];
    }
}
