<?php


namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    //  Get All Users
    public function index(Request $request): JsonResponse
    {
        $users = $this->userService->getAllUsers($request->query('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => $users
        ], Response::HTTP_OK);
    }

    //  Get Single User by ID
    public function show(int $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ], Response::HTTP_OK);
    }

    //  Get Single User by ID
    public function showProfile(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' =>  $user = Auth::user(),
        ], Response::HTTP_OK);
    }

    // Update User
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $updatedUser = $this->userService->updateUser($user, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $updatedUser
        ], Response::HTTP_OK);
    }

    public function updateProfile(UpdateUserRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated=$request->validated();
        unset($validated['role_id'], $validated['phone']); // Prevent role & phone updates

        $updatedUser = $this->userService->updateUser($user, $validated);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $updatedUser
        ], Response::HTTP_OK);
    }

    //  Delete User
    public function destroy(int $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $this->userService->deleteUser($user);

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ], Response::HTTP_NO_CONTENT);
    }
}
