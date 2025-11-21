<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Knuckles\Scribe\Attributes\{
    Authenticated,
    Header,
    BodyParam,
    Response,
    UrlParam,
    ResponseFromApiResource
};
use PHPUnit\Metadata\Api\Groups;

class PostController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct(private readonly PostService $postService) {}

    /**
     * GET /posts
     *
     * This endpoint retrieves a paginated list of all products.
     * Requires authentication with a valid Bearer token.
     */
    #[Groups(['Posts'])]
    #[Authenticated]
    #[UrlParam(name: 'page', type: 'integer', description: 'The page number for pagination.', example: 1)]
    #[Header(name: 'Authorization', example: 'Bearer your_access_token_here')]
    #[ResponseFromApiResource(
        name: PostResource::class,
        model: Post::class,
        collection: true,
        status: 200,
        description: 'A collection of post resources.'
    )]
    public function index(): JsonResponse
    {
        $products = $this->postService->getAll();

        // Return a collection of resources
        return PostResource::collection($products)->response();
    }

    /**
     * POST /posts
     *
     * Stores a new post record in the database, assigned to the authenticated user.
     * Returns the created post resource.
     */
    #[
        Groups(['Posts']),
        Authenticated,
        Header(name: 'Authorization', example: 'Bearer your_access_token_here'),
        BodyParam(name: 'content', type: 'string', description: 'The post content.', example: 'Check out this way to make your coffee'),
        BodyParam(name: 'product_id', type: 'number', description: 'The product being posted about content.', example: '14'),
        ResponseFromApiResource(
            name: PostResource::class,
            model: Post::class,
            status: 201,
            description: 'The newly created post resource.'
        ),
        Response(
            content: ['message' => 'The given data was invalid.', 'errors' => ['name' => ['The name field is required.']]],
            status: 422,
            description: 'Validation failed.'
        )
    ]
    public function store(StorePostRequest $request): JsonResponse
    {
        $data = $request->validated();

        $post = $this->postService->createPost($data);

        // Return a single resource with a 201 Created status
        return (new PostResource($post))->response()->setStatusCode(201);
    }

    /**
     * GET /posts/{post}
     *
     * Fetches the details for a single post using its ID.
     */
    #[
        Groups(['Posts']),
        Authenticated,
        Header(name: 'Authorization', example: 'Bearer your_access_token_here'),
        ResponseFromApiResource(
            name: PostResource::class,
            model: Post::class,
            status: 200,
            description: 'The requested post resource.'
        ),
        Response(
            content: ['message' => 'No query results for model [App\\Models\\Post] 404'],
            status: 404,
            description: 'Post not found.'
        )
    ]
    public function show(Post $post): JsonResponse
    {
        return (new PostResource($post))->response();
    }

    /**
     * PUT/PATCH /posts/{post}
     *
     * Modifies the details of an existing post. Requires user to be the owner.
     */
    #[
        Groups(['Posts']),
        Authenticated,
        Header(name: 'Authorization', example: 'Bearer your_access_token_here'),
        BodyParam(name: 'content', type: 'string', description: 'The updated post content.', example: 'Updated: new way to make your coffee'),
        ResponseFromApiResource(
            name: PostResource::class,
            model: Post::class,
            status: 200,
            description: 'The updated post resource.'
        ),
        Response(
            content: ['message' => 'This action is unauthorized.'],
            status: 403,
            description: 'Forbidden (Not the post owner).'
        )
    ]
    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        $post = $this->postService->updatePost($post, $request->validated());

        return (new PostResource($post))->response();
    }

    /**
     * DELETE /posts/{post}
     *
     * Permanently removes a post from the database. Requires user to be the owner.
     */
    #[
        Groups(['Posts']),
        Authenticated,
        Header(name: 'Authorization', example: 'Bearer your_access_token_here'),
        Response(
            status: 204,
            description: 'No Content (Post successfully deleted).'
        ),
        Response(
            content: ['message' => 'This action is unauthorized.'],
            status: 403,
            description: 'Forbidden (Not the post owner).'
        )
    ]
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $this->postService->deletePost($post);

        return response()->json(null, 204);
    }
}
