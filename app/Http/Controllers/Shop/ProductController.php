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
     *     summary="Get a filtered list of products",
     *     operationId="getFilteredProducts",
     *     tags={"Products"},
     *
     *     @OA\Parameter(
     *          name="search",
     *          in="query",
     *          required=false,
     *          description="Search query to look for matches",
     *          @OA\Schema(type="string", example="samsung")
     *      ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         required=false,
     *         description="Filter by category ID",
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *     @OA\Parameter(
     *         name="price_min",
     *         in="query",
     *         required=false,
     *         description="Minimum price filter",
     *         @OA\Schema(type="number", format="float", example=10.50)
     *     ),
     *     @OA\Parameter(
     *         name="price_max",
     *         in="query",
     *         required=false,
     *         description="Maximum price filter",
     *         @OA\Schema(type="number", format="float", example=299.99)
     *     ),
     *     @OA\Parameter(
     *        name="attributes[1][]",
     *        in="query",
     *        required=false,
     *        description="Filter by attribute ID 1 (example value IDs: 1, 2)",
     *        @OA\Schema(type="array", @OA\Items(type="integer"))
     *     ),
     *     @OA\Parameter(
     *         name="attributes[2][]",
     *         in="query",
     *         required=false,
     *         description="Filter by attribute ID 2 (example value IDs: 3, 4)",
     *         @OA\Schema(type="array", @OA\Items(type="integer"))
     *     ),
     *     @OA\Parameter(
     *         name="attributes[3][]",
     *         in="query",
     *         required=false,
     *         description="Filter by attribute ID 3 (example value IDs: 5, 6)",
     *         @OA\Schema(type="array", @OA\Items(type="integer"))
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         description="Field to sort by",
     *         @OA\Schema(type="string", enum={"price", "created_at", "sales", "rating"}, example="price")
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         in="query",
     *         required=false,
     *         description="Sort direction",
     *         @OA\Schema(type="string", enum={"asc", "desc"}, example="asc")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Items per page",
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         description="Filter by active status",
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Filtered product list",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
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
