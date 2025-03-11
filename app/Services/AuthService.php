<?php
namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    protected UserRepository $userRepository;
    protected OTPService $otpService;

    public function __construct(UserRepository $userRepository, OTPService $otpService)
    {
        $this->userRepository = $userRepository;
        $this->otpService = $otpService;
    }

    /**
     * send Otp to user's phone.
     */
    public function sendOTP($phone): void
    {
        $this->otpService->sendOTP($phone);
    }

    /**
     * verify otp and register(if needed) and login.
     */
    public function verifyOTP($phone, $code)
    {
        if ($this->otpService->verifyOTP($phone, $code)) {
            $user = $this->userRepository->findByPhone($phone);
            if (!$user) {
                $user = $this->userRepository->create([
                    'phone' => $phone,
                    'phone_verified_at' => now() // Set verification timestamp on creation
                ]);
            }
            return $user->createToken('authToken')->plainTextToken;
        }
        return null;
    }

    /**
     * update password .
     */
    public function updatePassword($password, $code,$user): bool
    {
        if ($this->otpService->verifyOTP($user->phone, $code)) {
            $this->userRepository->update($user,['password' => $password]);
            return true;
        }
        return false;
    }


    /**
     * login with email and password.
     */
    public function loginWithPassword($email, $password)
    {
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            return Auth::user()->createToken('authToken')->plainTextToken;
        }
        return null;
    }
}
