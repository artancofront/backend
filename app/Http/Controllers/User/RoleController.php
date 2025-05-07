<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Services\RoleService;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Admin Roles",
 *     description="Role management"
 * )
 */
class RoleController extends Controller
{
    protected RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * @OA\Get(
     *     path="/api/admin/roles",
     *     summary="Get all roles",
     *     tags={"Admin Roles"},
     *     security={{"BearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of roles",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Role"))
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        return response()->json($this->roleService->getAllRoles());
    }

    /**
     * @OA\Get(
     *     path="/api/admin/roles/{id}",
     *     summary="Get a specific role by ID",
     *     tags={"Admin Roles"},
     *     security={{"BearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Role ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role details",
     *         @OA\JsonContent(ref="#/components/schemas/Role")
     *     ),
     *     @OA\Response(response=404, description="Role not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        return response()->json($this->roleService->getRoleById($id));
    }

    /**
     * @OA\Post(
     *     path="/api/admin/roles",
     *     summary="Create a new role",
     *     tags={"Admin Roles"},
     *     security={{"BearerAuth":{}}},
     *     @OA\RequestBody(ref="#/components/requestBodies/StoreOrUpdateRoleRequest"),
     *     @OA\Response(
     *         response=201,
     *         description="Role created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Role")
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(RoleRequest $request): JsonResponse
    {
        $role = $this->roleService->createRole($request->validated());
        return response()->json($role, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/roles/{id}",
     *     summary="Update an existing role",
     *     tags={"Admin Roles"},
     *     security={{"BearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Role ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(ref="#/components/requestBodies/StoreOrUpdateRoleRequest"),
     *     @OA\Response(
     *         response=200,
     *         description="Role updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Role")
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=404, description="Role not found")
     * )
     */
    public function update(RoleRequest $request, int $id): JsonResponse
    {
        $role = $this->roleService->updateRole($id, $request->validated());
        return response()->json($role);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/roles/{id}",
     *     summary="Delete a role",
     *     tags={"Admin Roles"},
     *     security={{"BearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Role ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Role deleted successfully"),
     *     @OA\Response(response=404, description="Role not found")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $this->roleService->deleteRole($id);
        return response()->json(['message' => 'Role deleted successfully.']);
    }
}

