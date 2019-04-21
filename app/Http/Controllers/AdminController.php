<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Event;
use App\EventCategory;
use App\User;
use App\Issue;

class AdminController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the admin dashboard for handling users.
     *
     * @return \Illuminate\Http\Response
     */
    public function users() {
        if(!Auth::user()->is_admin) { // TODO: Change this to use policies
            return abort(401, 'You do not possess the required permissions to acces the administration pages');
        }
        return view('pages.admin.users', ['users' => User::paginate(10)]);
    }

    /**
     * Show the admin dashboard for handling events.
     *
     * @return \Illuminate\Http\Response
     */
    public function events() {
        if(!Auth::user()->is_admin) { // TODO: Change this to use policies
            return abort(401, 'You do not possess the required permissions to acces the administration pages');
        }
        return view('pages.admin.events', ['events' => Event::paginate(10)]);
    }

    /**
     * Show the admin dashboard for handling events.
     *
     * @return \Illuminate\Http\Response
     */
    public function issues() {
        if(!Auth::user()->is_admin) { // TODO: Change this to use policies
            return abort(401, 'You do not possess the required permissions to acces the administration pages');
        }
        return view('pages.admin.issues', ['issues' => Issue::paginate(10)]);
    }

    /**
     * Show the admin dashboard for handling event categories.
     *
     * @return \Illuminate\Http\Response
     */
    public function categories() {
        if(!Auth::user()->is_admin) { // TODO: Change this to use policies
            return abort(401, 'You do not possess the required permissions to acces the administration pages');
        }
        return view('pages.admin.categories', ['categories' => EventCategory::paginate(10)]);
    }

    /**
     * Disables a certain event
     */
    public function disableEvent(Request $request) {
        $this->authorize('disable', Event::class);

        $event_id = $request->input('id');

        // TODO: Discuss the responses

        if (empty($event_id)) {
            return response('Missing parameter "id"', 400)
                            ->header('Content-Type', 'text/plain');
        }

        $event = Event::find($event_id);

        if (is_null($event)) {
            return response('Event with id "' . $id . '" not found.', 404)
                            ->header('Content-Type', 'text/plain');
        }

        $event->is_disabled = true;
        $event->save();

        return response(200);
    }

    /**
     * Enables a certain event
     */
    public function enableEvent(Request $request) {
        $this->authorize('enable', Event::class);

        $event_id = $request->input('id');

        // TODO: Discuss the responses

        if (empty($event_id)) {
            return response('Missing parameter "id"', 400)
                            ->header('Content-Type', 'text/plain');
        }

        $event = Event::find($event_id);

        if (is_null($event)) {
            return response('Event with id "' . $id . '" not found.', 404)
                            ->header('Content-Type', 'text/plain');
        }

        $event->is_disabled = false;
        $event->save();

        return response(200);
    }

    /**
     * Bans a certain user
     */
    public function banUser(Request $request) {

    }

    /**
     * Unbans a certain user
     */
    public function unbanUser(Request $request) {

    }

    /**
     * Closes a certain issue
     */
    public function closeIssue(Request $request) {

    }
}
