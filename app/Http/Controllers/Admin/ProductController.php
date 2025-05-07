<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Product\VariantRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Services\MediaService;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    protected ProductService $productService;
    protected MediaService $mediaService;

    public function __construct(ProductService $productService, MediaService $mediaService)
    {
        $this->productService = $productService;
        $this->mediaService = $mediaService;
    }

    /**
     * @OA\Post(
     *     path="/api/admin/products",
     *     summary="Create a new product",
     *     tags={"Admin Products"},
     *     security={{"BearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreProductRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully."
     *     )
     * )
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->createBasicProduct($request->validated());

        return response()->json([
            'message' => 'Product created successfully.',
            'data' => $product
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/products/{id}",
     *     summary="Update an existing product",
     *     tags={"Admin Products"},
     *     security={{"BearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateProductRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully."
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request, validation failed."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found."
     *     )
     * )
     */

    public function update(UpdateProductRequest $request,int $id): JsonResponse
    {
//        $data=$request->validate([
//        ]);
        $this->productService->updateProductWithRelations($id,$request->validated());

        return response()->json([
            'message' => 'Product updated successfully.'
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/products/{id}",
     *     summary="Delete a product",
     *     tags={"Admin Products"},
     *     security={{"BearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully."
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $this->productService->deleteProduct($id);

        return response()->json([
            'message' => 'Product deleted successfully.'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/products/{parentId}/variants",
     *     summary="Add a variant to a product",
     *     tags={"Admin Products"},
     *     security={{"BearerAuth":{}}},
     *     @OA\Parameter(
     *         name="parentId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/VariantRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Variant added."
     *     )
     * )
     */
    public function storeVariant(VariantRequest $request, int $parentId)
    {
        $variant = $this->productService->addVariant($parentId, $request->validated());
        return response()->json($variant);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/products/variants/{variantId}",
     *     summary="Update a product variant",
     *     tags={"Admin Products"},
     *     security={{"BearerAuth":{}}},
     *     @OA\Parameter(
     *         name="variantId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/VariantRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Variant updated."
     *     )
     * )
     */
    public function updateVariant(VariantRequest $request, int $variantId)
    {
        $variant = $this->productService->updateVariant($variantId, $request->validated());
        return response()->json($variant);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/products/variants/{variantId}",
     *     summary="Delete a variant",
     *     tags={"Admin Products"},
     *     security={{"BearerAuth":{}}},
     *     @OA\Parameter(
     *         name="variantId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Variant deleted."
     *     )
     * )
     */
    public function deleteVariant(int $variantId)
    {
        $this->productService->deleteVariant($variantId);
        return response()->json(['message' => 'Variant deleted']);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/products/upload-image/upload",
     *     summary="Upload a product image to temp",
     *     tags={"Admin Products"},
     *     security={{"BearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"image"},
     *                 @OA\Property(property="image", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Image uploaded in temp successfully."
     *     )
     * )
     */
    public function uploadProductImage(Request $request)
    {
        $this->mediaService->cleanOldTempFilesIfDue(5);

        $request->validate([
            'image' => 'required|file|image|max:5120',
        ]);

        $file = $request->file('image');
        $userId = auth()->id();
        $path = $this->mediaService->uploadToTemp($file, $userId);

        return response()->json([
            'message' => 'Image uploaded in temp successfully.',
            'path' => $path,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/products/{id}/image",
     *     summary="Attach uploaded image to product",
     *     tags={"Admin Products"},
     *     security={{"BearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"image_path"},
     *             @OA\Property(property="image_path", type="string"),
     *             @OA\Property(property="order", type="integer"),
     *             @OA\Property(property="is_primary", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Image successfully attached to product."
     *     )
     * )
     */
    public function attachImageToProduct(Request $request, int $id)
    {
        $request->validate([
            'image_path' => 'required|string',
            'order' => 'nullable|integer',
            'is_primary' => 'nullable|boolean',
        ]);

        $finalPath = $this->mediaService->moveToFinal('image_path', "products/{$id}");

        $this->productService->saveProductImage($id, [
            'path' => $finalPath,
            'order' => $request->input('order'),
            'is_primary' => $request->boolean('is_primary'),
        ]);

        return response()->json([
            'message' => 'Image successfully attached to product.',
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/products/image/{id}",
     *     summary="Update image metadata",
     *     tags={"Admin Products"},
     *     security={{"BearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="order", type="integer"),
     *             @OA\Property(property="is_primary", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Image updated successfully."
     *     )
     * )
     */
    public function updateImage(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'order' => 'nullable|integer',
            'is_primary' => 'nullable|boolean',
        ]);

        $this->productService->updateImage($id, $data);

        return response()->json(['message' => 'Image updated successfully.']);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/products/image/{id}",
     *     summary="Delete product image",
     *     tags={"Admin Products"},
     *     security={{"BearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Image deleted successfully."
     *     )
     * )
     */
    public function destroyImage(int $id): JsonResponse
    {
        $this->productService->deleteImage($id);

        return response()->json(['message' => 'Image deleted successfully.']);
    }
}
