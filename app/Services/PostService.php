<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PostService
{
    public function getAll(): LengthAwarePaginator
    {
        return Post::paginate();
    }

    public function createPost(array $data): Post
    {
        return Post::create($data);
    }

    public function updatePost(Post $post, array $data): Post
    {
        $post->update($data);
        return $post;
    }

    /**
     * Delete a post.
     */
    public function deletePost(Post $post): bool
    {
        return $post->delete();
    }
}
