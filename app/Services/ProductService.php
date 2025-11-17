<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    /**
     * Retrieve all products (or paginate them).
     */
    public function getAll(): LengthAwarePaginator
    {
        return Product::paginate();
    }

    /**
     * Create a new product.
     */
    public function createProduct(array $data): Model
    {
        return Product::create($data);
    }

    /**
     * Retrieve a single product by its ID.
     */
    public function getProductById(int $id): Model
    {
        return Product::findOrFail($id);
    }

    /**
     * Update an existing product.
     */
    public function updateProduct(Product $product, array $data): Product
    {
        $product->update($data);
        return $product;
    }

    /**
     * Delete a product.
     */
    public function deleteProduct(Product $product): bool
    {
        return $product->delete();
    }
}
