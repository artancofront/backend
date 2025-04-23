<?php

namespace App\Services;

use App\Repositories\CustomerRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerAuthService
{
    protected CustomerRepository $customerRepository;
    protected OTPService $otpService;

    public function __construct(CustomerRepository $customerRepository, OTPService $otpService)
    {
        $this->customerRepository = $customerRepository;
        $this->otpService = $otpService;
    }

    /**
     * Send OTP to customer's phone.
     */
    public function sendOTP(string $phone): void
    {
        $this->otpService->sendOTP($phone);
    }

    /**
     * Verify OTP and register (if needed), then login.
     */
    public function verifyOTP(string $phone, string $code): ?string
    {
        if ($this->otpService->verifyOTP($phone, $code)) {
            $customer = $this->customerRepository->findByPhone($phone);

            if (!$customer) {
                $customer = $this->customerRepository->create([
                    'phone' => $phone,
                    'phone_verified_at' => now(),
                ]);
            }

            return $customer->createToken('authToken')->plainTextToken;
        }

        return null;
    }

    /**
     * Update password using OTP verification.
     */
    public function updatePassword(string $password, string $code, $customer): bool
    {
        if ($this->otpService->verifyOTP($customer->phone, $code)) {
            $this->customerRepository->update($customer, [
                'password' => Hash::make($password)
            ]);
            return true;
        }

        return false;
    }

    /**
     * Login using email and password (guard: customer).
     */
    public function loginWithPassword(string $email, string $password): ?string
    {
        if (Auth::guard('customer')->attempt(['email' => $email, 'password' => $password])) {
            return Auth::guard('customer')->user()->createToken('authToken')->plainTextToken;
        }

        return null;
    }
}
