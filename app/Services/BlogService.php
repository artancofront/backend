<?php

namespace App\Services;

use App\Repositories\BlogRepository;

class BlogService
{
    public function __construct(protected BlogRepository $blogRepository) {}

    public function listBlogs()
    {
        return $this->blogRepository->paginate();
    }

    public function getBlog(int $id)
    {
        return $this->blogRepository->find($id);
    }

    public function getBlogBySlug(string $slug)
    {
        return $this->blogRepository->findBySlug($slug);
    }

    public function createBlog(array $data)
    {
        // Optionally handle slug generation or published_at auto-fill
        return $this->blogRepository->create($data);
    }

    public function updateBlog(int $id, array $data)
    {
        return $this->blogRepository->update($id, $data);
    }

    public function deleteBlog(int $id)
    {
        return $this->blogRepository->delete($id);
    }
}
