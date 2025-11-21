<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * @inheritDoc
     */
    public function definition(): array {
        return [
            'content' => $this->faker->sentence(),
            'product_id' => Product::factory(),
            'image_path' => 'posts/' . $this->faker->uuid() . '.jpg',
        ];
    }
}
