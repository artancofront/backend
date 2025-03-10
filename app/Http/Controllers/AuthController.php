<?php
namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\AskOTPRequest;
use App\Http\Requests\VerifyOTPRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Get User's phone number and send them an OTP.
     */
    public function askOTP(AskOTPRequest $request): JsonResponse
    {
        $request->validated();
        $this->authService->sendOTP($request->phone);

        return response()->json([
            'success' => true,
            'message' => 'OTP sent for registration'
        ], Response::HTTP_OK);
    }

    /**
     * Verify OTP and return token.
     */
    public function verifyOTP(VerifyOTPRequest $request): JsonResponse
    {
        $request->validated();
        $token = $this->authService->verifyOTP($request->phone, $request->otp);

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'success' => true,
            'token' => $token
        ], Response::HTTP_OK);
    }

    /**
     * Reset password using OTP.
     */
    public function resetPasswordWithOTP(ResetPasswordRequest $request): JsonResponse
    {
        $request->validated();
        $user = $request->user();

        if (!$this->authService->updatePassword($request->password, $request->otp, $user)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully'
        ], Response::HTTP_OK);
    }

    /**
     * Login with email and password.
     */
    public function loginWithPassword(LoginRequest $request): JsonResponse
    {
        $request->validated();
        $token = $this->authService->loginWithPassword($request->email, $request->password);

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'success' => true,
            'token' => $token
        ], Response::HTTP_OK);
    }
}
