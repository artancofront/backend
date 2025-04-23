<?php

namespace App\Repositories;

use App\Models\Blog;

class BlogRepository
{
    public function all($with = [])
    {
        return Blog::with($with)->latest()->get();
    }

    public function paginate($perPage = 15, $with = [])
    {
        return Blog::with($with)->latest()->paginate($perPage);
    }

    public function find(int $id, $with = [])
    {
        return Blog::with($with)->findOrFail($id);
    }

    public function findBySlug(string $slug, $with = [])
    {
        return Blog::with($with)->where('slug', $slug)->firstOrFail();
    }

    public function create(array $data): Blog
    {
        return Blog::create($data);
    }

    public function update(int $id, array $data): Blog
    {
        $blog = $this->find($id);
        $blog->update($data);
        return $blog;
    }

    public function delete(int $id): bool
    {
        return Blog::destroy($id) > 0;
    }
}
