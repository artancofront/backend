<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\ProductCommentRatingService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Customer Product Comment Ratings",
 *     description="Endpoints for managing customer product comments and ratings"
 * )
 */
class CustomerProductCommentRatingController extends Controller
{
    protected ProductCommentRatingService $service;

    public function __construct(ProductCommentRatingService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     *     path="/api/customer/product-comments/{productId}",
     *     tags={"Customer Product Comment Ratings"},
     *     summary="List comments and ratings for a product",
     *     @OA\Parameter(
     *         name="productId",
     *         in="path",
     *         required=true,
     *         description="ID of the product",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of comments"
     *     )
     * )
     */
    public function index(int $productId)
    {
        $perPage = request()->get('per_page', 15);

        return response()->json($this->service->getByProduct($productId, true, $perPage));
    }

    /**
     * @OA\Get(
     *     path="/api/customer/product-comments/view/{id}",
     *     tags={"Customer Product Comment Ratings"},
     *     summary="Get a specific comment and rating",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Comment ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Comment details"),
     *     @OA\Response(response=404, description="Comment not found")
     * )
     */
    public function show(int $id)
    {
        $comment = $this->service->find($id);

        return $comment
            ? response()->json($comment)
            : response()->json(['message' => 'Comment not found'], 404);
    }

    /**
     * @OA\Post(
     *     path="/api/customer/product-comments",
     *     tags={"Customer Product Comment Ratings"},
     *     summary="Create a new comment and rating",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id", "rating"},
     *             @OA\Property(property="product_id", type="integer"),
     *             @OA\Property(property="comment", type="string", maxLength=1000),
     *             @OA\Property(property="rating", type="integer", minimum=1, maximum=5)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Comment created")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'comment' => 'nullable|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $validated['customer_id'] = auth('customer')->id();

        return response()->json($this->service->create($validated), 201);
    }

    /**
     * @OA\Put(
     *     path="/api/customer/product-comments/{id}",
     *     tags={"Customer Product Comment Ratings"},
     *     summary="Update a customer's comment",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Comment ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="comment", type="string", maxLength=1000),
     *             @OA\Property(property="rating", type="integer", minimum=1, maximum=5)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Update result"),
     *     @OA\Response(response=403, description="Unauthorized or not found")
     * )
     */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'comment' => 'nullable|string|max:1000',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $comment = $this->service->find($id);

        if (!$comment || $comment->customer_id !== auth('customer')->id()) {
            return response()->json(['message' => 'Unauthorized or comment not found'], 403);
        }

        return response()->json([
            'success' => $this->service->update($id, $validated),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/customer/product-comments/{id}",
     *     tags={"Customer Product Comment Ratings"},
     *     summary="Delete a customer's comment",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Comment ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Delete result"),
     *     @OA\Response(response=403, description="Unauthorized or not found")
     * )
     */
    public function destroy(int $id)
    {
        $comment = $this->service->find($id);

        if (!$comment || $comment->customer_id !== auth('customer')->id()) {
            return response()->json(['message' => 'Unauthorized or comment not found'], 403);
        }

        return response()->json([
            'success' => $this->service->delete($id),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/customer/product-comments/{id}/like",
     *     tags={"Customer Product Comment Ratings"},
     *     summary="Like a comment",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Like successful")
     * )
     */
    public function like(int $id)
    {
        return response()->json(['success' => $this->service->like($id)]);
    }

    /**
     * @OA\Post(
     *     path="/api/customer/product-comments/{id}/dislike",
     *     tags={"Customer Product Comment Ratings"},
     *     summary="Dislike a comment",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Dislike successful")
     * )
     */
    public function dislike(int $id)
    {
        return response()->json(['success' => $this->service->dislike($id)]);
    }

    /**
     * @OA\Post(
     *     path="/api/customer/product-comments/{id}/unlike",
     *     tags={"Customer Product Comment Ratings"},
     *     summary="Remove like from a comment",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Unlike successful")
     * )
     */
    public function unlike(int $id)
    {
        return response()->json(['success' => $this->service->unlike($id)]);
    }

    /**
     * @OA\Post(
     *     path="/api/customer/product-comments/{id}/undislike",
     *     tags={"Customer Product Comment Ratings"},
     *     summary="Remove dislike from a comment",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Undislike successful")
     * )
     */
    public function undislike(int $id)
    {
        return response()->json(['success' => $this->service->undislike($id)]);
    }
}
