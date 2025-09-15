<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AskOTPRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\VerifyOTPRequest;
use App\Services\CustomerAuthService;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Customer Authentication",
 *     description="Authentication endpoints for customers"
 * )
 */
class AuthController extends Controller
{
    protected CustomerAuthService $authService;
    protected CustomerService $customerService;

    public function __construct(CustomerAuthService $authService,CustomerService $customerService)
    {
        $this->authService = $authService;
        $this->customerService = $customerService;
    }

    /**
     * @OA\Post(
     *     path="/api/customers/ask-otp",
     *     summary="Request OTP for phone number",
     *     tags={"Customer Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="phone", type="string", example="09123456789")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully"
     *     )
     * )
     */
    public function askOTP(AskOTPRequest $request): JsonResponse
    {
        $this->authService->sendOTP($request->phone);

        return response()->json([
            'success' => true,
            'message' => 'OTP sent for registration'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/customers/verify-otp",
     *     summary="Verify OTP and login/register",
     *     tags={"Customer Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="phone", type="string", example="09123456789"),
     *             @OA\Property(property="otp", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful, token returned"
     *     )
     * )
     */
    public function verifyOTP(VerifyOTPRequest $request): JsonResponse
    {
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
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/customers/reset-password",
     *     summary="Reset customer password using OTP",
     *     tags={"Customer Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="phone", type="string", example="09123456789"),
     *             @OA\Property(property="otp", type="string", example="123456"),
     *             @OA\Property(property="password", type="string", example="newpass"),
     *             @OA\Property(property="password_confirmation", type="string", example="newpass")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password updated successfully"
     *     )
     * )
     */
    public function resetPasswordWithOTP(ResetPasswordRequest $request): JsonResponse
    {
        $customer = $request->user('customer');

        if (!$this->authService->updatePassword($request->password, $request->otp, $customer)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/customers/login-password",
     *     summary="Login using email and password",
     *     tags={"Customer Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", example="customer@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful"
     *     )
     * )
     */
    public function loginWithPassword(LoginRequest $request): JsonResponse
    {
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
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/customers/show-profile",
     *     summary="Get the authenticated customer's profile",
     *     description="Retrieve the currently authenticated customer's profile",
     *     tags={"Customer Profile"},
     *     @OA\Response(
     *         response=200,
     *         description="Customer profile retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/User"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function showProfile(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Auth::guard('customer')->user()->load('addresses'),
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/customers/update-profile",
     *     summary="Update authenticated customer's profile",
     *     description="Update the currently authenticated customer's profile",
     *     tags={"Customer Profile"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Customer profile data to update",
     *         @OA\JsonContent(ref="#/components/schemas/UpdateProfileRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Customer profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Customer updated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/User"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $customer = Auth::guard('customer')->user();
        $validated = $request->validated();

        unset($validated['phone'], $validated['password']); // Prevent role, phone, password update

        $updatedCustomer = $this->customerService->update($customer, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Customer updated successfully',
            'data' => $updatedCustomer
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/customers/logout",
     *     summary="Logout customers",
     *     description="Revoke the customers's current access token and log them out.",
     *     tags={"Customer Authentication"},
     *     @OA\Response(
     *         response=200,
     *         description="Customers logged out successfully",
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
