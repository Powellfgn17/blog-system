<?php

namespace App\Policies;

use App\Models\User;

class ReactionPolicy
{
    public function create(User $user): bool
    {
        return ! $user->isBlocked();
    }
}
