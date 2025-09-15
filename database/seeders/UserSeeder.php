<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as FakerFactory;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create('fa_IR');
        $roles = Role::all();

        foreach ($roles as $role) {
            User::create([
                'phone' => '9' . $faker->unique()->numerify('#########'),
                'email' => $faker->unique()->safeEmail(),
                'name' => $faker->name(),
                'phone_verified_at' => now(),
                'password' => Hash::make('password'),
                'role_id' => $role->id,
                'remember_token' => Str::random(10),
            ]);
        }
    }
}
