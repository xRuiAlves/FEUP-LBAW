<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the username.
     *
     * @param  \App\User  $user
     */
    public function updateName(User $user)
    {
        // Any logged user may update its name
        return Auth::check();
    }

    /**
     * Determine if the user may delete their account.
     *
     * @param  \App\User  $user
     */
    public function deleteAccount(User $user)
    {
        // Any logged user may delete its account
        return Auth::check();
    }

    /**
     * Determine if the user may promote other users to admin
     *
     * @param  \App\User  $user
     */
    public function promoteToAdmin(User $user)
    {
        // Only admins may promote other users to admin
        return $user->is_admin;
    }

    /**
     * Determine if the user may enable/disable other users accounts
     *
     * @param  \App\User  $user
     */
    public function enableDisable(User $user)
    {
        // Only admins may enable/disable other users accounts
        return $user->is_admin;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function view(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function update(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function delete(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function restore(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function forceDelete(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can mark an event as favorite.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function markFavorite(User $user)
    {
        return Auth::check($user);
    }
}
