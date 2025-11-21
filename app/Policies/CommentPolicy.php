<?php

namespace App\Policies;

use App\Models\{Comment, User};

class CommentPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Comment $comment): bool
    {
        return $comment->user->is($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Comment $comment): bool
    {
        return $comment->user->is($user);
    }
}
