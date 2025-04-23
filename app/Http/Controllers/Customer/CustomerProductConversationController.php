<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\ProductConversationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Customer Product Conversations",
 *     description="Customer endpoints for managing product conversations"
 * )
 */
class CustomerProductConversationController extends Controller
{
    protected ProductConversationService $service;

    public function __construct(ProductConversationService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     *     path="/api/customer/product-conversations/{productId}",
     *     tags={"Customer Product Conversations"},
     *     summary="Get approved conversations for a product",
     *     security={{"customerAuth":{}}},
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
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(response=200, description="List of conversations")
     * )
     */
    public function index(Request $request, int $productId): JsonResponse
    {
        $conversations = $this->service->getConversationsByProduct(
            $productId,
            true,
            $request->get('per_page', 15)
        );

        return response()->json($conversations);
    }

    /**
     * @OA\Post(
     *     path="/api/customer/product-conversations",
     *     tags={"Customer Product Conversations"},
     *     summary="Create a new conversation or reply",
     *     security={{"customerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id", "message"},
     *             @OA\Property(property="product_id", type="integer"),
     *             @OA\Property(property="parent_id", type="integer", nullable=true),
     *             @OA\Property(property="message", type="string", maxLength=2000)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Conversation created")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'parent_id' => 'nullable|integer|exists:product_conversations,id',
            'message' => 'required|string|max:2000',
        ]);

        $data['customer_id'] = Auth::guard('customer')->id();

        $conversation = $this->service->createConversation($data);

        return response()->json($conversation, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/customer/product-conversations/{id}",
     *     tags={"Customer Product Conversations"},
     *     summary="Update a conversation (only if owned by customer)",
     *     security={{"customerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"message"},
     *             @OA\Property(property="message", type="string", maxLength=2000)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Conversation updated"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $conversation = $this->service->getConversation($id);

        if (!$conversation || $conversation->customer_id !== Auth::guard('customer')->id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $data = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $this->service->updateConversation($id, $data);

        return response()->json(['message' => 'Conversation updated.']);
    }

    /**
     * @OA\Delete(
     *     path="/api/customer/product-conversations/{id}",
     *     tags={"Customer Product Conversations"},
     *     summary="Delete a conversation (only if owned by customer)",
     *     security={{"customerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Conversation deleted"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $conversation = $this->service->getConversation($id);

        if (!$conversation || $conversation->customer_id !== Auth::guard('customer')->id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $this->service->deleteConversation($id);

        return response()->json(['message' => 'Conversation deleted.']);
    }

    /**
     * @OA\Post(
     *     path="/api/customer/product-conversations/{id}/like",
     *     tags={"Customer Product Conversations"},
     *     summary="Like a conversation",
     *     security={{"customerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Liked")
     * )
     */
    public function like(int $id): JsonResponse
    {
        $success = $this->service->likeConversation($id);
        return response()->json(['success' => $success]);
    }

    /**
     * @OA\Post(
     *     path="/api/customer/product-conversations/{id}/dislike",
     *     tags={"Customer Product Conversations"},
     *     summary="Dislike a conversation",
     *     security={{"customerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Disliked")
     * )
     */
    public function dislike(int $id): JsonResponse
    {
        $success = $this->service->dislikeConversation($id);
        return response()->json(['success' => $success]);
    }

    /**
     * @OA\Post(
     *     path="/api/customer/product-conversations/{id}/undo-like",
     *     tags={"Customer Product Conversations"},
     *     summary="Undo a like",
     *     security={{"customerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Undo like success")
     * )
     */
    public function undoLike(int $id): JsonResponse
    {
        $success = $this->service->undoLikeConversation($id);
        return response()->json(['success' => $success]);
    }

    /**
     * @OA\Post(
     *     path="/api/customer/product-conversations/{id}/undo-dislike",
     *     tags={"Customer Product Conversations"},
     *     summary="Undo a dislike",
     *     security={{"customerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Undo dislike success")
     * )
     */
    public function undoDislike(int $id): JsonResponse
    {
        $success = $this->service->undoDislikeConversation($id);
        return response()->json(['success' => $success]);
    }
}
