<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Categories",
 *     description="Category management endpoints for the admin panel"
 * )
 */
class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Get all root categories",
     *     tags={"Categories"},
     *     @OA\Response(response=200, description="List of root categories")
     * )
     */
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getRootCategories();
        return response()->json($categories);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/categories",
     *     summary="Create a new category",
     *     tags={"Categories"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CategoryRequest")
     *     ),
     *     @OA\Response(response=201, description="Category created successfully")
     * )
     */
    public function store(CategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->create($request->validated());
        return response()->json($category, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{id}",
     *     summary="Get a specific category",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id", in="path", required=true, @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Category found"),
     *     @OA\Response(response=404, description="Category not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json($category);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/categories/{id}",
     *     summary="Update a category",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id", in="path", required=true, @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CategoryRequest")
     *     ),
     *     @OA\Response(response=200, description="Category updated successfully"),
     *     @OA\Response(response=404, description="Category not found or not updated")
     * )
     */
    public function update(CategoryRequest $request, int $id): JsonResponse
    {
        $updated = $this->categoryService->update($id, $request->validated());

        if (!$updated) {
            return response()->json(['message' => 'Category not found or not updated'], 404);
        }

        return response()->json(['message' => 'Category updated successfully']);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/categories/{id}",
     *     summary="Delete a category",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id", in="path", required=true, @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Category deleted successfully"),
     *     @OA\Response(response=404, description="Category not found or not deleted")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->categoryService->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Category not found or not deleted'], 404);
        }

        return response()->json(['message' => 'Category deleted successfully']);
    }

    /**
     * @OA\Get(
     *     path="/api/categories/hierarchy",
     *     summary="Get category hierarchy",
     *     tags={"Categories"},
     *     @OA\Response(response=200, description="Hierarchy retrieved")
     * )
     */
    public function hierarchy(): JsonResponse
    {
        return response()->json($this->categoryService->getHierarchy());
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{id}/breadcrumb",
     *     summary="Get breadcrumb for a category",
     *     tags={"Categories"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Breadcrumb retrieved")
     * )
     */
    public function breadcrumb(int $id): JsonResponse
    {
        return response()->json($this->categoryService->getBreadcrumb($id));
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{id}/descendants",
     *     summary="Get descendants of a category",
     *     tags={"Categories"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Descendants retrieved")
     * )
     */
    public function descendants(int $id): JsonResponse
    {
        return response()->json($this->categoryService->getDescendants($id));
    }

    /**
     * @OA\Get(
     *     path="/api/categories/leaf",
     *     summary="Get all leaf categories",
     *     tags={"Categories"},
     *     @OA\Response(response=200, description="Leaf categories retrieved")
     * )
     */
    public function leaf(): JsonResponse
    {
        return response()->json($this->categoryService->getLeafCategories());
    }

    /**
     * @OA\Get(
     *     path="/api/admin/categories/{id}/products",
     *     summary="Get products of a category",
     *     tags={"Categories"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Products retrieved")
     * )
     */
    public function products(int $id): JsonResponse
    {
        return response()->json($this->categoryService->getCategoryProducts($id));
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{id}/attributes",
     *     summary="Get attributes of a category",
     *     tags={"Categories"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Attributes retrieved")
     * )
     */
    public function attributes(int $id): JsonResponse
    {
        return response()->json($this->categoryService->getCategoryAttributes($id));
    }
}
