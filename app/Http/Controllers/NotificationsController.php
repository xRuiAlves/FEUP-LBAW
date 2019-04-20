<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Notification;

class NotificationsController extends Controller
{
    public function show() {
        if (!Auth::check()) {
            return redirect('/login');
        }

        // TODO:
        // Supposedly should be working but does not, test more in depth later
        // $this->authorize('list', Notification::class);

        $notifications = Auth::user()
            ->notifications()
            ->notSeen()
            ->orderBy('timestamp', 'desc')
            ->paginate(10);

        return view('pages.notifications', ['notifications' => $notifications]);
    }
}
