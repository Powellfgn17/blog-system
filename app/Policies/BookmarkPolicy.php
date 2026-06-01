<?php

namespace App\Policies;

use App\Models\User;

class BookmarkPolicy
{
    public function create(User $user): bool
    {
        return ! $user->isBlocked();
    }
}
