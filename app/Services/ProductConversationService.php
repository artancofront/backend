<?php

namespace App\Services;

use App\Repositories\ProductConversationRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\ProductConversation;

class ProductConversationService
{
    protected ProductConversationRepository $repository;

    public function __construct(ProductConversationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllConversations(?bool $isApproved = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAll($isApproved, $perPage);
    }

    public function getConversation(int $id): ?ProductConversation
    {
        return $this->repository->find($id);
    }

    public function getConversationsByProduct(int $productId, ?bool $isApproved = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getByProduct($productId, $isApproved, $perPage);
    }

    public function getReplies(int $parentId, ?bool $isApproved = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getReplies($parentId, $isApproved, $perPage);
    }

    public function createConversation(array $data): ProductConversation
    {
        return $this->repository->create($data);
    }

    public function updateConversation(int $id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    public function deleteConversation(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function approveConversation(int $id): bool
    {
        return $this->repository->setApprovalStatus($id, true);
    }

    public function disapproveConversation(int $id): bool
    {
        return $this->repository->setApprovalStatus($id, false);
    }

    public function likeConversation(int $id): bool
    {
        return $this->repository->like($id);
    }

    public function dislikeConversation(int $id): bool
    {
        return $this->repository->dislike($id);
    }

    public function undoLikeConversation(int $id): bool
    {
        return $this->repository->undoLike($id);
    }

    public function undoDislikeConversation(int $id): bool
    {
        return $this->repository->undoDislike($id);
    }

    /**
     * Add an admin reply to a product conversation.
     */
    public function addAdminReply(int $conversationId, array $data)
    {
        return $this->repository->addAdminReply($conversationId, $data);
    }

    /**
     * Delete an admin reply by its ID.
     */
    public function deleteAdminReply(int $replyId): bool
    {
        return $this->repository->deleteAdminReply($replyId);
    }
}
