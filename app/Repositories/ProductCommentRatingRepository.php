<?php
namespace App\Repositories;

use App\Models\ProductCommentRating;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductCommentRatingRepository
{
    /**
     * Get all product comments (with optional approval filter).
     */
    public function getAll(?bool $isApproved = null, int $perPage = 15): LengthAwarePaginator
    {
        return ProductCommentRating::when(!is_null($isApproved), function ($query) use ($isApproved) {
            $query->where('is_approved', $isApproved);
        })
            ->whereNotNull('comment') // Ensure it has a comment
            ->with(['product', 'user', 'adminReplies'])
            ->paginate($perPage);
    }

    /**
     * Find a comment by its ID.
     */
    public function find(int $id)
    {
        return ProductCommentRating::with(['product', 'user', 'adminReplies'])->find($id);
    }

    /**
     * Get comments for a specific product.
     */
    public function getByProduct(int $productId, ?bool $isApproved = null, int $perPage = 15): LengthAwarePaginator
    {
        return ProductCommentRating::where('product_id', $productId)
            ->when(!is_null($isApproved), function ($query) use ($isApproved) {
                $query->where('is_approved', $isApproved);
            })
            ->whereNotNull('comment') // Ensure it has a comment
            ->with(['user', 'adminReplies'])
            ->paginate($perPage);
    }

    /**
     * Create a new product comment.
     */
    public function create(array $data): ProductCommentRating
    {
        return ProductCommentRating::create($data);
    }

    /**
     * Update an existing comment.
     */
    public function update(int $id, array $data): bool
    {
        $comment = $this->find($id);
        return $comment ? $comment->update($data) : false;
    }

    /**
     * Delete a comment.
     */
    public function delete(int $id): bool
    {
        $comment = $this->find($id);
        return $comment ? $comment->delete() : false;
    }

    /**
     * Approve or disapprove a comment.
     */
    public function setApprovalStatus(int $id, bool $status): bool
    {
        return $this->update($id, ['is_approved' => $status]);
    }




}
