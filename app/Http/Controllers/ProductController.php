<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductService;
use PHPUnit\Metadata\Api\Groups;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Auth\Guard;
use App\Http\Resources\ProductResource;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\UrlParam;
use Knuckles\Scribe\Attributes\BodyParam;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Knuckles\Scribe\Attributes\Authenticated;
use Illuminate\Routing\Controller as BaseController;
use Knuckles\Scribe\Attributes\Header;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

class ProductController extends BaseController
{

    // Inject the service via the constructor for dependency injection
    public function __construct(protected ProductService $productService) {}

    /**
     * GET /products
     *
     * This endpoint retrieves a paginated list of all products.
     * Requires authentication with a valid Bearer token.
     */
    #[Groups(['Products'])]
    #[Authenticated]
    #[UrlParam(name: 'page', type: 'integer', description: 'The page number for pagination.', example: 1)]
    #[Header(name: 'Authorization', example: 'Bearer your_access_token_here')]
    #[ResponseFromApiResource(
        name: ProductResource::class,
        model: Product::class,
        collection: true,
        status: 200,
        description: 'A collection of product resources.'
    )]
    public function index(): JsonResponse
    {
        $products = $this->productService->getAll();

        // Return a collection of resources
        return Product::collection($products)->response();
    }

    /**
     * POST /products
     *
     * Stores a new product record in the database, assigned to the authenticated user.
     * Returns the created product resource.
     */
    #[Groups(['Products'])]
    #[Authenticated]
    #[Header(name: 'Authorization', example: 'Bearer your_access_token_here')]
    #[BodyParam(name: 'name', type: 'string', required: true, description: 'The product name.', example: 'Organic Coffee Beans')]
    #[BodyParam(name: 'description', type: 'string', required: false, description: 'A detailed description of the product.', example: 'Sourced from high-altitude farms in Colombia.')]
    #[BodyParam(name: 'price', type: 'number', required: true, description: 'The price of the product.', example: 19.99)]
    #[ResponseFromApiResource(
        name: ProductResource::class,
        model: Product::class,
        status: 201,
        description: 'The newly created product resource.'
    )]
    #[Response(
        content: ['message' => 'The given data was invalid.', 'errors' => ['name' => ['The name field is required.']]],
        status: 422,
        description: 'Validation failed.'
    )]
    public function store(StoreProductRequest $request, Guard $auth): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = $auth->id();

        $product = $this->productService->createProduct($data);

        // Return a single resource with a 201 Created status
        return (new ProductResource($product))->response()->setStatusCode(201);
    }

    /**
     * GET /products/{product}
     *
     * Fetches the details for a single product using its ID.
     */
    #[Groups(['Products'])]
    #[Authenticated]
    #[Header(name: 'Authorization', example: 'Bearer your_access_token_here')]
    #[UrlParam(name: 'product', type: 'integer', description: 'The ID of the product.', example: 5)]
    #[ResponseFromApiResource(
        name: ProductResource::class,
        model: Product::class,
        status: 200,
        description: 'The requested product resource.'
    )]
    #[Response(
        content: ['message' => 'No query results for model [App\\Models\\Product] 404'],
        status: 404,
        description: 'Product not found.'
    )]
    public function show(Product $product): JsonResponse
    {
        // The Product model is automatically resolved (Route Model Binding)
        return (new ProductResource($product))->response();
    }

    /**
     * PUT/PATCH /products/{product}
     *
     * Modifies the details of an existing product. Requires user to be the owner.
     */
    #[Groups(['Products'])]
    #[Authenticated]
    #[Header(name: 'Authorization', example: 'Bearer your_access_token_here')]
    #[UrlParam(name: 'product', type: 'integer', description: 'The ID of the product to update.', example: 5)]
    #[BodyParam(name: 'name', type: 'string', required: false, description: 'The updated product name.', example: 'Premium Coffee Beans')]
    #[BodyParam(name: 'price', type: 'number', required: false, description: 'The updated price.', example: 21.50)]
    #[ResponseFromApiResource(
        name: ProductResource::class,
        model: Product::class,
        status: 200,
        description: 'The updated product resource.'
    )]
    #[Response(
        content: ['message' => 'This action is unauthorized.'],
        status: 403,
        description: 'Forbidden (Not the product owner).'
    )]
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        // $this->authorize('update', $product);

        $product = $this->productService->updateProduct($product, $request->validated());

        return (new ProductResource($product))->response();
    }

    /**
     * DELETE /products/{product}
     *
     * Permanently removes a product from the database. Requires user to be the owner.
     */
    #[Groups(['Products'])]
    #[Authenticated]
    #[Header(name: 'Authorization', example: 'Bearer your_access_token_here')]
    #[UrlParam(name: 'product', type: 'integer', description: 'The ID of the product to delete.', example: 5)]
    #[Response(
        status: 204,
        description: 'No Content (Product successfully deleted).'
    )]
    #[Response(
        content: ['message' => 'This action is unauthorized.'],
        status: 403,
        description: 'Forbidden (Not the product owner).'
    )]
    public function destroy(Product $product): JsonResponse
    {
        // this->authorize('delete', $product);

        $this->productService->deleteProduct($product);

        return response()->json(null, 204); // 204 No Content for successful deletion
    }
}
