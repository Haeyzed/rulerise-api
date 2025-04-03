<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BlogPostRequest;
use App\Http\Resources\BlogPostResource;
use App\Models\BlogPost;
use App\Services\BlogPostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogPostController extends Controller
{
    /**
     * @var BlogPostService
     */
    protected BlogPostService $blogPostService;

    /**
     * BlogPostController constructor.
     *
     * @param BlogPostService $blogPostService
     */
    public function __construct(BlogPostService $blogPostService)
    {
        $this->blogPostService = $blogPostService;
//        $this->authorizeResource(BlogPost::class, 'blogPost');
    }

    /**
     * Display a listing of the blog posts.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'search', 'category_id', 'tag_ids', 'author_id', 'is_published',
            'sort_by', 'sort_direction', 'trashed'
        ]);

        $blogPosts = $this->blogPostService->list($filters, $request->input('per_page', 15));

        return response()->paginatedSuccess(
            BlogPostResource::collection($blogPosts),
            'Blog posts retrieved successfully'
        );
    }

    /**
     * Store a newly created blog post in storage.
     *
     * @param BlogPostRequest $request
     * @return JsonResponse
     */
    public function store(BlogPostRequest $request): JsonResponse
    {
        $blogPost = $this->blogPostService->create($request->validated());

        return response()->created(
            new BlogPostResource($blogPost),
            'Blog post created successfully'
        );
    }

    /**
     * Display the specified blog post.
     *
     * @param BlogPost $blogPost
     * @return JsonResponse
     */
    public function show(BlogPost $blogPost): JsonResponse
    {
        // Increment views count
        $blogPost->incrementViewsCount();

        // Load relationships
        $blogPost->load(['category', 'tags', 'author']);

        // Get related posts
        $relatedPosts = $this->blogPostService->getRelatedPosts($blogPost);

        return response()->success(
            [
                'blog_post' => new BlogPostResource($blogPost),
                'related_posts' => BlogPostResource::collection($relatedPosts)
            ],
            'Blog post retrieved successfully'
        );
    }

    /**
     * Update the specified blog post in storage.
     *
     * @param BlogPostRequest $request
     * @param BlogPost $blogPost
     * @return JsonResponse
     */
    public function update(BlogPostRequest $request, BlogPost $blogPost): JsonResponse
    {
        $blogPost = $this->blogPostService->update($blogPost, $request->validated());

        return response()->success(
            new BlogPostResource($blogPost),
            'Blog post updated successfully'
        );
    }

    /**
     * Remove the specified blog post from storage.
     *
     * @param BlogPost $blogPost
     * @return JsonResponse
     */
    public function destroy(BlogPost $blogPost): JsonResponse
    {
        $this->blogPostService->delete($blogPost);

        return response()->success(null, 'Blog post deleted successfully');
    }

    /**
     * Force delete the specified blog post from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function forceDelete(int $id): JsonResponse
    {
        $blogPost = BlogPost::withTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $blogPost);

        $this->blogPostService->forceDelete($blogPost);

        return response()->success(null, 'Blog post permanently deleted successfully');
    }

    /**
     * Restore the specified blog post.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function restore(int $id): JsonResponse
    {
        $blogPost = BlogPost::withTrashed()->findOrFail($id);
        $this->authorize('restore', $blogPost);

        $this->blogPostService->restore($blogPost);

        return response()->success(
            new BlogPostResource($blogPost),
            'Blog post restored successfully'
        );
    }

    /**
     * Get blog post statistics.
     *
     * @return JsonResponse
     */
    public function getStatistics(): JsonResponse
    {
        $this->authorize('viewAny', BlogPost::class);

        $stats = $this->blogPostService->getStatistics();

        return response()->success($stats, 'Blog post statistics retrieved successfully');
    }

    /**
     * Get trending blog posts.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getTrendingPosts(Request $request): JsonResponse
    {
        $this->authorize('viewAny', BlogPost::class);

        $limit = $request->input('limit', 5);
        $trendingPosts = $this->blogPostService->getTrendingPosts($limit);

        return response()->success(
            BlogPostResource::collection($trendingPosts),
            'Trending blog posts retrieved successfully'
        );
    }

    /**
     * Get featured blog posts.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getFeaturedPosts(Request $request): JsonResponse
    {
        $this->authorize('viewAny', BlogPost::class);

        $limit = $request->input('limit', 5);
        $featuredPosts = $this->blogPostService->getFeaturedPosts($limit);

        return response()->success(
            BlogPostResource::collection($featuredPosts),
            'Featured blog posts retrieved successfully'
        );
    }

    /**
     * Publish the specified blog post.
     *
     * @param Request $request
     * @param BlogPost $blogPost
     * @return JsonResponse
     */
    public function publish(Request $request, BlogPost $blogPost): JsonResponse
    {
        $this->authorize('update', $blogPost);

        $request->validate([
            'published_at' => 'nullable|date',
        ]);

        $publishedAt = $request->input('published_at') ? new \DateTime($request->input('published_at')) : null;
        $blogPost = $this->blogPostService->publish($blogPost, $publishedAt);

        return response()->success(
            new BlogPostResource($blogPost),
            'Blog post published successfully'
        );
    }

    /**
     * Unpublish the specified blog post.
     *
     * @param BlogPost $blogPost
     * @return JsonResponse
     */
    public function unpublish(BlogPost $blogPost): JsonResponse
    {
        $this->authorize('update', $blogPost);

        $blogPost = $this->blogPostService->unpublish($blogPost);

        return response()->success(
            new BlogPostResource($blogPost),
            'Blog post unpublished successfully'
        );
    }

    /**
     * Archive the specified blog post.
     *
     * @param BlogPost $blogPost
     * @return JsonResponse
     */
    public function archive(BlogPost $blogPost): JsonResponse
    {
        $this->authorize('update', $blogPost);

        $blogPost = $this->blogPostService->archive($blogPost);

        return response()->success(
            new BlogPostResource($blogPost),
            'Blog post archived successfully'
        );
    }
}

