<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProductConversationService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Admin Product Conversations",
 *     description="APIs for managing product conversations in the admin panel."
 * )
 */
class AdminProductConversationController extends Controller
{
    protected ProductConversationService $service;

    public function __construct(ProductConversationService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     *     path="/api/admin/product-conversations",
     *     tags={"Admin Product Conversations"},
     *     summary="Get all product conversations",
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
     *         description="List of product conversations"
     *     )
     * )
     */
    public function getAllConversations(Request $request)
    {
        $isApproved = $request->boolean('is_approved', null);
        $perPage = $request->get('per_page', 15);

        return response()->json($this->service->getAllConversations($isApproved, $perPage));
    }

    /**
     * @OA\Get(
     *     path="/api/admin/product-conversations/{id}",
     *     tags={"Admin Product Conversations"},
     *     summary="Get a specific product conversation",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the conversation",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product conversation details"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Conversation not found"
     *     )
     * )
     */
    public function getConversation(int $id)
    {
        $conversation = $this->service->getConversation($id);

        return $conversation
            ? response()->json($conversation)
            : response()->json(['message' => 'Conversation not found'], 404);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/product-conversations/product/{productId}",
     *     tags={"Admin Product Conversations"},
     *     summary="Get product conversations by product ID",
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
     *         description="List of product conversations for the specified product"
     *     )
     * )
     */

    public function getConversationsByProduct(Request $request, int $productId)
    {
        $isApproved = $request->boolean('is_approved', null);
        $perPage = $request->get('per_page', 15);

        return response()->json($this->service->getConversationsByProduct($productId, $isApproved, $perPage));
    }

    /**
     * @OA\Get(
     *     path="/api/admin/product-conversations/{conversationId}/replies",
     *     tags={"Admin Product Conversations"},
     *     summary="Get replies for a conversation",
     *     @OA\Parameter(
     *         name="conversationId",
     *         in="path",
     *         required=true,
     *         description="The ID of the conversation",
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
     *         description="List of replies for the specified conversation"
     *     )
     * )
     */
    
    public function getReplies(Request $request, int $conversationId)
    {
        $isApproved = $request->boolean('is_approved', null);
        $perPage = $request->get('per_page', 15);

        return response()->json($this->service->getReplies($conversationId, $isApproved, $perPage));
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/product-conversations/{id}",
     *     tags={"Admin Product Conversations"},
     *     summary="Delete a product conversation",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the conversation",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Conversation deleted"
     *     )
     * )
     */
    public function deleteConversation(int $id)
    {
        return response()->json(['success' => $this->service->deleteConversation($id)]);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/product-conversations/{id}/approval",
     *     tags={"Admin Product Conversations"},
     *     summary="Approve a product conversation",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the conversation",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Conversation approved"
     *     )
     * )
     */
    public function approveConversation(int $id)
    {
        return response()->json(['success' => $this->service->approveConversation($id)]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/product-conversations/{id}/admin-reply",
     *     tags={"Admin Product Conversations"},
     *     summary="Add an admin reply to a conversation",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the conversation",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"text"},
     *             @OA\Property(property="text", type="string", description="Reply text")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Admin reply added"
     *     )
     * )
     */
    public function addAdminReply(Request $request, int $id)
    {
        $validated = $request->validate([
            'text' => 'required|string',
        ]);

        return response()->json([
            'success' => $this->service->addAdminReply($id, $validated),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/product-conversations/{conversationId}/admin-reply/{replyId}",
     *     tags={"Admin Product Conversations"},
     *     summary="Delete an admin reply from a conversation",
     *     @OA\Parameter(
     *         name="conversationId",
     *         in="path",
     *         required=true,
     *         description="The ID of the conversation",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="replyId",
     *         in="path",
     *         required=true,
     *         description="The ID of the reply",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Admin reply deleted"
     *     )
     * )
     */
    public function deleteAdminReply(int $conversationId, int $replyId)
    {
        return response()->json([
            'success' => $this->service->deleteAdminReply($replyId),
        ]);
    }
}
