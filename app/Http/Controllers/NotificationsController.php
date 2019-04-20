<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

class NotificationsController extends Controller
{
    public function show($id) {
        $user = User::find($id);
        $notifications = $user->notifications;

        return $notifications;
    }
}
