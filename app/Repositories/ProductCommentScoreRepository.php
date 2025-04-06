<?php
namespace App\Repositories;

use App\Models\ProductCommentScore;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductCommentScoreRepository
{
    /**
     * Get all product comments (with optional approval filter).
     */
    public function getAll(?bool $isApproved = null, int $perPage = 15): LengthAwarePaginator
    {
        return ProductCommentScore::when(!is_null($isApproved), function ($query) use ($isApproved) {
            $query->where('is_approved', $isApproved);
        })
            ->whereNotNull('comment') // Ensure it has a comment
            ->with(['product', 'user'])
            ->paginate($perPage);
    }

    /**
     * Find a comment by its ID.
     */
    public function find(int $id): ?ProductCommentScore
    {
        return ProductCommentScore::with(['product', 'user'])->find($id);
    }

    /**
     * Get comments for a specific product.
     */
    public function getByProduct(int $productId, ?bool $isApproved = null, int $perPage = 15): LengthAwarePaginator
    {
        return ProductCommentScore::where('product_id', $productId)
            ->when(!is_null($isApproved), function ($query) use ($isApproved) {
                $query->where('is_approved', $isApproved);
            })
            ->whereNotNull('comment') // Ensure it has a comment
            ->with('user')
            ->paginate($perPage);
    }

    /**
     * Create a new product comment.
     */
    public function create(array $data): ProductCommentScore
    {
        return ProductCommentScore::create($data);
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
