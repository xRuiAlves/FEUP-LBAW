<?php

namespace App\Policies;

use App\User;
use App\Issue;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class IssuePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create issues.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        // Any user can create a new issue
        return Auth::check();
    }

    /**
     * Determine whether the user can solve issues.
     * Not passing in a specific issue because if he can solve one, he can solve all of them (must be admin)
     */
    public function solve(User $user) {
        return $user->is_admin;
    }
}
