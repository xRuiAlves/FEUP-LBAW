<?php

namespace App\Policies;

use App\User;
use App\Notification;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class NotificationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the notification.
     *
     * @param  \App\User  $user
     * @param  \App\Notification  $notification
     * @return mixed
     */
    public function view(User $user, Notification $notification)
    {
        // N/A? TODO: Discuss
    }

    /**
     * Determine whether the user can list their notification.
     *
     * @param  \App\User  $user
     * @param  \App\Notification  $notification
     * @return mixed
     */
    public function list(User $user)
    {
      // Any user can list their own notifications
      return Auth::check();
    }

    /**
     * Determine whether the user can create notifications.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        // TODO: Discuss
    }

    /**
     * Determine whether the user can update the notification.
     *
     * @param  \App\User  $user
     * @param  \App\Notification  $notification
     * @return mixed
     */
    public function update(User $user, Notification $notification)
    {
        // TODO: Discuss
    }

    /**
     * Determine whether the user can delete the notification.
     *
     * @param  \App\User  $user
     * @param  \App\Notification  $notification
     * @return mixed
     */
    public function delete(User $user, Notification $notification)
    {
        // TODO: Discuss
    }
}
