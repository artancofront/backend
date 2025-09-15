<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BlogService;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Admin Blogs",
 *     description="Admin blog management"
 * )
 */
class BlogController extends Controller
{
    public function __construct(protected BlogService $blogService) {}

    /**
     * @OA\Get(
     *     path="/api/blogs",
     *     tags={"Blogs"},
     *     summary="List blogs",
     *     @OA\Response(
     *         response=200,
     *         description="List of blogs"
     *     )
     * )
     */
    public function index()
    {
        return response()->json($this->blogService->listBlogs());
    }

    /**
     * @OA\Post(
     *     path="/api/admin/blogs",
     *     tags={"Admin Blogs"},
     *     summary="Create a new blog",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "slug", "content"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="slug", type="string"),
     *             @OA\Property(property="excerpt", type="string"),
     *             @OA\Property(property="content", type="string"),
     *             @OA\Property(property="cover_image", type="string"),
     *             @OA\Property(property="author_id", type="integer"),
     *             @OA\Property(property="status", type="string", enum={"draft", "published"}),
     *             @OA\Property(property="published_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Blog created successfully"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'regex:/^[\p{Arabic}a-zA-Z0-9\-]+$/u',
                'unique:blogs,slug',
            ],
            'excerpt'       => 'nullable|string',
            'content'       => 'required|string',
            'cover_image'   => 'nullable|string',
            'author_id'     => 'nullable|exists:users,id',
            'status'        => 'required|in:draft,published',
            'published_at'  => 'nullable|date',
        ]);

        $blog = $this->blogService->createBlog($data);

        return response()->json($blog, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/blogs/{id}",
     *     tags={"Blogs"},
     *     summary="Get a single blog by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Blog found"),
     *     @OA\Response(response=404, description="Blog not found")
     * )
     */
    public function show(int $id)
    {
        return response()->json($this->blogService->getBlog($id));
    }

    /**
     * @OA\Put(
     *     path="/api/admin/blogs/{id}",
     *     tags={"Admin Blogs"},
     *     summary="Update a blog",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="slug", type="string"),
     *             @OA\Property(property="excerpt", type="string"),
     *             @OA\Property(property="content", type="string"),
     *             @OA\Property(property="cover_image", type="string"),
     *             @OA\Property(property="author_id", type="integer"),
     *             @OA\Property(property="status", type="string", enum={"draft", "published"}),
     *             @OA\Property(property="published_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Blog updated"),
     *     @OA\Response(response=404, description="Blog not found")
     * )
     */
    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'title'         => 'sometimes|string|max:255',
            'slug' => [
                'required',
                'string',
                'regex:/^[\p{Arabic}a-zA-Z0-9\-]+$/u',
                'unique:blogs,slug,' . $id,
            ],
            'excerpt'       => 'nullable|string',
            'content'       => 'sometimes|string',
            'cover_image'   => 'nullable|string',
            'author_id'     => 'nullable|exists:users,id',
            'status'        => 'sometimes|in:draft,published',
            'published_at'  => 'nullable|date',
        ]);

        $blog = $this->blogService->updateBlog($id, $data);

        return response()->json($blog);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/blogs/{id}",
     *     tags={"Admin Blogs"},
     *     summary="Delete a blog",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Deleted successfully"),
     *     @OA\Response(response=404, description="Blog not found")
     * )
     */
    public function destroy(int $id)
    {
        $this->blogService->deleteBlog($id);
        return response()->json(null, 204);
    }
}
