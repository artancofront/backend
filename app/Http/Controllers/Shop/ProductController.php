<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;

use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="List products with filters, sorting and pagination",
     *     tags={"Products"},
     *     security={{"BearerAuth":{}}},
     *     @OA\Parameter(name="filters", in="query", description="Search filters (e.g., name, category_id, etc.)", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="sort_by", in="query", description="Field to sort by", required=false, @OA\Schema(type="string", default="price")),
     *     @OA\Parameter(name="order", in="query", description="Sort order (asc or desc)", required=false, @OA\Schema(type="string", enum={"asc", "desc"}, default="asc")),
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", required=false, @OA\Schema(type="integer", default=15)),
     *     @OA\Parameter(name="status", in="query", description="Product status (1=active, 0=inactive)", required=false, @OA\Schema(type="boolean")),
     *     @OA\Response(response=200, description="List of products")
     * )
     */
    public function index(Request $request)
    {
        $filters = $request->except(['sort_by', 'order', 'per_page', 'status']);
        $sortBy = $request->get('sort_by', 'price');
        $order = $request->get('order', 'asc');
        $perPage = (int) $request->get('per_page', 15);
        $status = $request->has('status') ? filter_var($request->get('status'), FILTER_VALIDATE_BOOLEAN) : null;

        $products = $this->productService->getProductList($filters, $sortBy, $order, $perPage, $status);

        return response()->json($products);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Get product details by ID",
     *     tags={"Products"},
     *     security={{"BearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Product details"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function show(int $id)
    {
        $product = $this->productService->getProductById($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

}
