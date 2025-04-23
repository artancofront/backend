<?php

namespace App\Services;

use App\Repositories\ProductCommentRatingRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\ProductCommentRating;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductCommentRatingService
{
    protected ProductCommentRatingRepository $repository;

    public function __construct(ProductCommentRatingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAll(?bool $isApproved = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAll($isApproved, $perPage);
    }

    public function find(int $id): ?ProductCommentRating
    {
        return $this->repository->find($id);
    }

    public function getByProduct(int $productId, ?bool $isApproved = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getByProduct($productId, $isApproved, $perPage);
    }

    public function create(array $data): ProductCommentRating
    {
        $customerId = $data['customer_id'];
        $productId = $data['product_id'];

        // Check if already commented
        if ($this->hasAlreadyCommented($customerId, $productId)) {
            throw ValidationException::withMessages([
                'comment' => 'You have already commented on this product.',
            ]);
        }

        // Check if delivered
        if (!$this->hasDeliveredProduct($customerId, $productId)) {
            throw ValidationException::withMessages([
                'product' => 'You can only comment on products that were delivered to you.',
            ]);
        }

        return $this->repository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function setApprovalStatus(int $id, bool $status): bool
    {
        return $this->repository->setApprovalStatus($id, $status);
    }

    public function like(int $id): bool
    {
        return $this->repository->like($id);
    }

    public function dislike(int $id): bool
    {
        return $this->repository->dislike($id);
    }

    public function unlike(int $id): bool
    {
        return $this->repository->unlike($id);
    }

    public function undislike(int $id): bool
    {
        return $this->repository->undislike($id);
    }

    public function findByCustomerAndProduct(int $customerId, int $productId): ?ProductCommentRating
    {
        return ProductCommentRating::where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->first();
    }

    protected function hasAlreadyCommented(int $customerId, int $productId): bool
    {
        return ProductCommentRating::where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->exists();
    }

    protected function hasDeliveredProduct(int $customerId, int $productId): bool
    {
        return DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.customer_id', $customerId)
            ->where('orders.status', 'delivered') // Adjust status as needed
            ->where('order_items.product_id', $productId)
            ->exists();
    }

    /**
     * Add an admin reply to a product conversation.
     */
    public function addAdminReply(int $commentId, array $data)
    {
        return $this->repository->addAdminReply($commentId, $data);
    }

    /**
     * Delete an admin reply by its ID.
     */
    public function deleteAdminReply(int $replyId): bool
    {
        return $this->repository->deleteAdminReply($replyId);
    }
}
