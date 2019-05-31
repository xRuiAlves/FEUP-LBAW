<?php

namespace App\Policies;

use App\User;
use App\Event;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class EventPolicy {
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the event.
     *
     * @param  \App\User  $user
     * @param  \App\Event  $event
     * @return mixed
     */
    public function view(User $user, Event $event) {
        //
    }

    /**
     * Determine whether the user can create events.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user) {
        // Any user can create a new event
        return Auth::check();
    }

    /**
     * Determine whether the user can create event categories.
     */
    public function createCategory(User $user) {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can update the event.
     *
     * @param  \App\User  $user
     * @param  \App\Event  $event
     * @return mixed
     */
    public function update(User $user, Event $event) {
        // 
    }

    /**
     * Determine whether the user can delete the event.
     *
     * @param  \App\User  $user
     * @param  \App\Event  $event
     * @return mixed
     */
    public function delete(User $user, Event $event)
    {
        //
    }

    /**
     * Determine whether the user can enable events.
     * Not passing in a specific event because if he can enable one, he can enable all of them (must be admin)
     */
    public function enable(User $user) {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can disable events.
     * Not passing in a specific event because if he can disable one, he can disable all of them (must be admin)
     */
    public function disable(User $user) {
        return $user->is_admin;
    }
}