<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function findByPhone($phone)
    {
        return User::where('phone', $phone)->first();
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update(User $user, array $data)
    {
        $user->update($data);
        return $user;
    }
}

