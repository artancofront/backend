<?php

namespace App\Repositories;

use App\Models\ProductConversation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductConversationRepository
{
    /**
     * Get all product conversations (with optional approval filter).
     */
    public function getAll(?bool $isApproved = null, int $perPage = 15): LengthAwarePaginator
    {
        return ProductConversation::when(!is_null($isApproved), function ($query) use ($isApproved) {
            $query->where('is_approved', $isApproved);
        })
            ->whereNull('parent_id') // Only top-level questions
            ->with(['product', 'user', 'replies', 'adminReplies'])
            ->paginate($perPage);
    }



    /**
     * Find a conversation by its ID.
     */
    public function find(int $id)
    {
        return ProductConversation::with(['product', 'user', 'parent', 'replies', 'adminReplies'])->find($id);
    }

    /**
     * Get conversations for a specific product.
     */
    public function getByProduct(int $productId, ?bool $isApproved = null, int $perPage = 15): LengthAwarePaginator
    {
        return ProductConversation::where('product_id', $productId)
            ->whereNull('parent_id') // Only top-level questions
            ->when(!is_null($isApproved), function ($query) use ($isApproved) {
                $query->where('is_approved', $isApproved);
            })
            ->with(['user', 'replies', 'adminReplies'])
            ->paginate($perPage);
    }

    /**
     * Get replies for a specific conversation.
     */
    public function getReplies(int $parentId, ?bool $isApproved = null, int $perPage = 15): LengthAwarePaginator
    {
        return ProductConversation::where('parent_id', $parentId)
            ->when(!is_null($isApproved), function ($query) use ($isApproved) {
                $query->where('is_approved', $isApproved);
            })
            ->with(['user', 'replies', 'adminReplies'])
            ->paginate($perPage);
    }

    /**
     * Create a new product conversation.
     */
    public function create(array $data): ProductConversation
    {
        return ProductConversation::create($data);
    }

    /**
     * Update an existing conversation.
     */
    public function update(int $id, array $data): bool
    {
        $conversation = $this->find($id);
        return $conversation ? $conversation->update($data) : false;
    }

    /**
     * Delete a conversation.
     */
    public function delete(int $id): bool
    {
        $conversation = $this->find($id);
        return $conversation ? $conversation->delete() : false;
    }

    /**
     * Approve or disapprove a conversation.
     */
    public function setApprovalStatus(int $id, bool $status): bool
    {
        return $this->update($id, ['is_approved' => $status]);
    }
}
