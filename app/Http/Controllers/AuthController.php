<?php
namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\AskOTPRequest;
use App\Http\Requests\VerifyOTPRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }


    /**
     * Get User's phone number and send them an OTP.
     */
    public function askOTP(AskOTPRequest $request): \Illuminate\Http\JsonResponse
    {
        $request->validated();
        $this->authService->sendOTP($request->phone);
        return response()->json(['message' => 'OTP sent for registration']);
    }

    /**
     * Verify Otp is valid and response with token.
     */
    public function verifyOTP(VerifyOTPRequest $request): \Illuminate\Http\JsonResponse
    {
        $request->validated();
        $token = $this->authService->verifyOTP($request->phone, $request->otp);
        if ($token) {
            return response()->json(['token' => $token]);
        }
        return response()->json(['message' => 'Invalid OTP'], 400);
    }

    /**
     * Reset password with otp.
     */
    public function resetPasswordWithOTP(ResetPasswordRequest $request): \Illuminate\Http\JsonResponse
    {
        $request->validated();
        $user = $request->user();
        if ($this->authService->updatePassword($request->password, $request->otp,$user)) {
            return response()->json(['message' => 'Password updated successfully']);
        }
        return response()->json(['message' => 'Invalid OTP'], 400);
    }

    /**
     * Login with Email and Password as credentials.
     */
    public function loginWithPassword(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        $request->validated();
        $token = $this->authService->loginWithPassword($request->email, $request->password);
        if ($token) {
            return response()->json(['token' => $token]);
        }
        return response()->json(['message' => 'Invalid credentials'], 401);
    }
}
