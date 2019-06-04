<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

    /**
     * Dismisses a notification
     */
    public function dismiss(Request $request) {
        $validated_data = $request->validate([
            'notification_id' => 'required|integer'
        ]);

        $notification_id = $validated_data['notification_id'];
        
        try {
            $notification = Notification::findOrFail($notification_id);
            
            $this->authorize('dismiss', $notification);

            $notification->is_dismissed = true;
            $notification->save();

            return response(null, 200);
        } catch (ModelNotFoundException $err) {
            return response(null, 404);
        }
    }
}
