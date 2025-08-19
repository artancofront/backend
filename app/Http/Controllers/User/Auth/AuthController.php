<?php
namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AskOTPRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\VerifyOTPRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *      name="Admin Authentication",
 *      description="User Authentication endpoints for the admin panel"
 * )
 */
class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @OA\Post(
     *     path="/api/admin/users/ask-otp",
     *     summary="Request OTP for phone number",
     *     description="Request an OTP to be sent to the user's phone number for registration.",
     *     tags={"Admin Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User's phone number to send OTP",
     *         @OA\JsonContent(
     *             @OA\Property(property="phone", type="string", example="01234567890")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="OTP sent for registration")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid phone number format",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid phone number")
     *         )
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/admin/users/verify-otp",
     *     summary="Verify OTP and get a token",
     *     description="Verify OTP sent to the user's phone number and return a JWT token.",
     *     tags={"Admin Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User's phone number and OTP to verify",
     *         @OA\JsonContent(
     *             @OA\Property(property="phone", type="string", example="01234567890"),
     *             @OA\Property(property="otp", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP verified successfully and token returned",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="token", type="string", example="jwt-token-here")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid OTP",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid OTP")
     *         )
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/admin/users/reset-password",
     *     summary="Reset password using OTP",
     *     description="Reset a user's password using OTP sent to their phone.",
     *     tags={"Admin Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Password reset data including OTP and new password",
     *         @OA\JsonContent(
     *             @OA\Property(property="phone", type="string", example="1234567890"),
     *             @OA\Property(property="otp", type="string", example="123456"),
     *             @OA\Property(property="password", type="string", example="new-password"),
     *             @OA\Property(property="password_confirmation", type="string", example="new-password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Password updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid OTP",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid OTP")
     *         )
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/admin/users/login-password",
     *     summary="Login using email and password",
     *     description="Login with email and password and receive a JWT token.",
     *     tags={"Admin Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Login credentials (email and password)",
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful and token returned",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="token", type="string", example="jwt-token-here")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid credentials")
     *         )
     *     )
     * )
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

    /**
     * @OA\Post(
     *     path="/api/admin/users/logout",
     *     summary="Logout user",
     *     description="Revoke the user's current access token and log them out.",
     *     tags={"Admin Authentication"},
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logged out")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }

}

