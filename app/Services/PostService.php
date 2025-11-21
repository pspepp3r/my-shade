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

    public function createPost(array $data)
    {
        return true;
    }
}
