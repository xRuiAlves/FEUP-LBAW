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
     * Determine whether the user can dismiss the notification
     *
     * @param  \App\User  $user
     * @param  \App\Notification  $notification
     * @return mixed
     */
    public function dismiss(User $user, Notification $notification)
    {
        return $user->id == $notification->user_id;
    }
}
