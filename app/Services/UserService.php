<?php
namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers($excludeId,$perPage=10)
    {
        return $this->userRepository->paginate($excludeId,$perPage);
    }

    public function getUserById(int $id)
    {
        return $this->userRepository->findById($id);
    }

    public function updateUser(User $user, array $data): User
    {

        // Update the user with the data
        return $this->userRepository->update($user, $data);
    }


    public function deleteUser(User $user): ?bool
    {
        return $this->userRepository->delete($user);
    }
}
