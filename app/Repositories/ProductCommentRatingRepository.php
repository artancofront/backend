<?php
namespace App\Repositories;

use App\Models\AdminReply;
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
            ->with(['product', 'customer', 'adminReplies'])
            ->paginate($perPage);
    }

    /**
     * Find a comment by its ID.
     */
    public function find(int $id)
    {
        return ProductCommentRating::with(['product', 'customer', 'adminReplies'])->find($id);
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
            ->with(['customer', 'adminReplies'])
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


    /**
     * Increment likes for a comment.
     */
    public function like(int $id): bool
    {
        $comment = $this->find($id);
        if ($comment) {
            $comment->increment('likes');
            return true;
        }
        return false;
    }

    /**
     * Increment dislikes for a comment.
     */
    public function dislike(int $id): bool
    {
        $comment = $this->find($id);
        if ($comment) {
            $comment->increment('dislikes');
            return true;
        }
        return false;
    }

    /**
     * Optionally allow unliking/disliking.
     */
    public function unlike(int $id): bool
    {
        $comment = $this->find($id);
        if ($comment && $comment->likes > 0) {
            $comment->decrement('likes');
            return true;
        }
        return false;
    }

    public function undislike(int $id): bool
    {
        $comment = $this->find($id);
        if ($comment && $comment->dislikes > 0) {
            $comment->decrement('dislikes');
            return true;
        }
        return false;
    }

    /**
     * Add an admin reply to a product comment.
     */
    public function addAdminReply(int $commentId, array $data): AdminReply
    {
        $comment = ProductCommentRating::findOrFail($commentId);
        return $comment->adminReplies()->create($data);
    }

    /**
     * Delete an admin reply by its ID.
     */
    public function deleteAdminReply(int $replyId): bool
    {
        $reply = AdminReply::find($replyId);
        return $reply ? $reply->delete() : false;
    }



}
