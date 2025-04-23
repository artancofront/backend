<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProductCommentRatingService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Admin Product Comments",
 *     description="APIs for managing product comments and ratings in the admin panel."
 * )
 */
class AdminProductCommentRatingController extends Controller
{
    protected ProductCommentRatingService $service;

    public function __construct(ProductCommentRatingService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     *     path="/api/admin/product-comments",
     *     tags={"Admin Product Comments"},
     *     summary="Get all product comments",
     *     @OA\Parameter(
     *         name="is_approved",
     *         in="query",
     *         description="Filter by approval status",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of product comments"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $isApproved = $request->boolean('is_approved', null);
        $perPage = $request->get('per_page', 15);

        return response()->json($this->service->getAll($isApproved, $perPage));
    }

    /**
     * @OA\Get(
     *     path="/api/admin/product-comments/{productId}",
     *     tags={"Admin Product Comments"},
     *     summary="Get product comments by product ID",
     *     @OA\Parameter(
     *         name="productId",
     *         in="path",
     *         required=true,
     *         description="The ID of the product",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="is_approved",
     *         in="query",
     *         description="Filter by approval status",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of product comments for the specified product"
     *     )
     * )
     */
    public function getByProduct(Request $request, int $productId)
    {
        $isApproved = $request->boolean('is_approved', null);
        $perPage = $request->get('per_page', 15);

        return response()->json($this->service->getByProduct($productId, $isApproved, $perPage));
    }

    /**
     * @OA\Get(
     *     path="/api/admin/product-comments/{id}",
     *     tags={"Admin Product Comments"},
     *     summary="Show a specific product comment",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the comment",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product comment details"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comment not found"
     *     )
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
     * @OA\Delete(
     *     path="/api/admin/product-comments/{id}",
     *     tags={"Admin Product Comments"},
     *     summary="Delete a product comment",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the comment",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Delete result"
     *     )
     * )
     */
    public function destroy(int $id)
    {
        return response()->json(['success' => $this->service->delete($id)]);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/product-comments/{id}/approval",
     *     tags={"Admin Product Comments"},
     *     summary="Set approval status of a comment",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the comment",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="boolean", description="Approval status")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Approval status updated"
     *     )
     * )
     */
    public function setApprovalStatus(Request $request, int $id)
    {
        $validated = $request->validate([
            'status' => 'required|boolean',
        ]);

        return response()->json([
            'success' => $this->service->setApprovalStatus($id, $validated['status']),
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/admin/product-comments/{commentId}/admin-reply",
     *     tags={"Admin Product Comments"},
     *     summary="Add an admin reply to a product comment",
     *     @OA\Parameter(
     *         name="commentId",
     *         in="path",
     *         required=true,
     *         description="The ID of the product comment",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"text"},
     *             @OA\Property(property="text", type="string", description="The admin's reply text"),
     *             @OA\Property(property="product_comment_rating_id", type="integer", description="The ID of the related product comment rating"),
     *             @OA\Property(property="product_conversation_id", type="integer", description="The ID of the related product conversation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Admin reply created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input data"
     *     )
     * )
     */
    public function addAdminReply(Request $request, int $commentId)
    {
        $validated = $request->validate([
            'text' => 'required|string',
            'product_comment_rating_id' => 'nullable|integer|exists:product_comment_ratings,id',
            'product_conversation_id' => 'nullable|integer|exists:product_conversations,id',
        ]);

        return response()->json($this->service->addAdminReply($commentId, $validated), 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/product-comments/admin-reply/{replyId}",
     *     tags={"Admin Product Comments"},
     *     summary="Delete an admin reply",
     *     @OA\Parameter(
     *         name="replyId",
     *         in="path",
     *         required=true,
     *         description="The ID of the admin reply",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Admin reply deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Admin reply not found"
     *     )
     * )
     */
    public function deleteAdminReply(int $replyId)
    {
        return response()->json(['success' => $this->service->deleteAdminReply($replyId)]);
    }
}
