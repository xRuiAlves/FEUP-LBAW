<?php

namespace App\Policies;

use App\User;
use App\Rating;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class RatingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can rate a post.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function rate(User $user)
    {
        // Any user can comment an event's post
        return Auth::check();
    }
}
