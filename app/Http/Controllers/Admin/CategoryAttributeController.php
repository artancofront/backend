<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CategoryAttributeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class CategoryAttributeController extends Controller
{
    protected CategoryAttributeService $service;

    public function __construct(CategoryAttributeService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     *     path="/api/admin/category-attributes",
     *     summary="List all category attributes",
     *     tags={"Category Attributes"},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(): JsonResponse
    {
        return response()->json($this->service->getAll());
    }

    /**
     * @OA\Get(
     *     path="/api/admin/category-attributes/{id}",
     *     summary="Get a specific category attribute by ID",
     *     tags={"Category Attributes"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $attribute = $this->service->getById($id);
        return $attribute
            ? response()->json($attribute)
            : response()->json(['message' => 'Not found'], 404);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/category-attributes",
     *     summary="Create a new category attribute",
     *     tags={"Category Attributes"},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"category_id", "name"},
     *         @OA\Property(property="category_id", type="integer"),
     *         @OA\Property(property="name", type="string")
     *     )),
     *     @OA\Response(response=201, description="Created")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string',
        ]);

        $attribute = $this->service->create($data);

        return response()->json($attribute, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/category-attributes/{id}",
     *     summary="Update a category attribute",
     *     tags={"Category Attributes"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="name", type="string")
     *     )),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'sometimes|string',
        ]);

        $success = $this->service->update($id, $data);

        return $success
            ? response()->json(['message' => 'Updated'])
            : response()->json(['message' => 'Not found'], 404);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/category-attributes/{id}",
     *     summary="Delete a category attribute",
     *     tags={"Category Attributes"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Deleted"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $success = $this->service->delete($id);

        return $success
            ? response()->noContent()
            : response()->json(['message' => 'Not found'], 404);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/category-attributes/{id}/values",
     *     summary="Get all values for a specific category attribute",
     *     tags={"Category Attributes"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function getValues(int $id): JsonResponse
    {
        return response()->json($this->service->getValuesForAttribute($id));
    }

    /**
     * @OA\Post(
     *     path="/api/admin/category-attributes/values",
     *     summary="Create a new value for a category attribute",
     *     tags={"Category Attributes"},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"category_attribute_id", "value"},
     *         @OA\Property(property="category_attribute_id", type="integer"),
     *         @OA\Property(property="value", type="string")
     *     )),
     *     @OA\Response(response=201, description="Created")
     * )
     */
    public function createValue(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category_attribute_id' => 'required|exists:category_attributes,id',
            'value' => 'required|string',
        ]);

        $value = $this->service->createValue($data);

        return response()->json($value, 201);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/category-attributes/values/{id}",
     *     summary="Update a category attribute value",
     *     tags={"Category Attributes"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"value"},
     *         @OA\Property(property="value", type="string")
     *     )),
     *     @OA\Response(response=200, description="Updated")
     * )
     */
    public function updateValue(int $id, Request $request): JsonResponse
    {
        $data = $request->validate([
            'value' => 'required|string',
        ]);

        $value = $this->service->updateValue($id, $data);

        return response()->json($value);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/category-attributes/values/{id}",
     *     summary="Delete a category attribute value",
     *     tags={"Category Attributes"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Deleted"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function deleteValue(int $id): JsonResponse
    {
        $success = $this->service->deleteValue($id);

        return $success
            ? response()->noContent()
            : response()->json(['message' => 'Not found'], 404);
    }
}
