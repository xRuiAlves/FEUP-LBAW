<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

use App\Event;
use App\EventCategory;
use App\User;
use App\Issue;
use App\Notification;

class AdminController extends Controller {

    const ITEMS_PER_PAGE = 10;

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
    public function users(Request $request) {
        if(!Auth::user()->is_admin) { // TODO: Change this to use policies
            return abort(401, 'You do not possess the required permissions to acces the administration pages');
        }
        
        $search_query = $request->get('search');
        
        if(!empty($search_query)){
            $users = User::FTS($search_query)->orderBy('name')->paginate(AdminController::ITEMS_PER_PAGE);

            $users->withPath('?search='.$search_query);
        }else{
            $users = User::orderBy('name')->paginate(AdminController::ITEMS_PER_PAGE);
        }

        return view('pages.admin.users', ['users' => $users]);
    }

    /**
     * Show the admin dashboard for handling events.
     *
     * @return \Illuminate\Http\Response
     */
    public function events(Request $request) {
        if(!Auth::user()->is_admin) { // TODO: Change this to use policies
            return abort(401, 'You do not possess the required permissions to acces the administration pages');
        }

        $search_query = $request->get('search');
        
        $events = Event::WithCategory()
        ->when(!empty($search_query), function ($q) use ($search_query) {
            return Event::FTSScope($q, $search_query);
        })
        ->paginate(AdminController::ITEMS_PER_PAGE);

        $events->appends(['search' => $search_query]);
        

        return view('pages.admin.events', ['events' => $events]);
    }

    /**
     * Show the admin dashboard for handling events.
     *
     * @return \Illuminate\Http\Response
     */
    public function issues(Request $request) {
        if(!Auth::user()->is_admin) { // TODO: Change this to use policies
            return abort(401, 'You do not possess the required permissions to acces the administration pages');
        }

        $search_query = $request->get('search');
        
        if(!empty($search_query)){
            $issues = Issue::FTS($search_query)->paginate(AdminController::ITEMS_PER_PAGE);

            $issues->withPath('?search='.$search_query);
        }else{
            $issues = Issue::paginate(AdminController::ITEMS_PER_PAGE);
        }

        return view('pages.admin.issues', ['issues' => $issues]);
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

        $categories = EventCategory::orderBy('id')->paginate(AdminController::ITEMS_PER_PAGE);
        return view('pages.admin.categories', ['categories' => $categories]);
    }

    /**
     * Disables a certain event
     */
    public function disableEvent(Request $request) {
        $this->authorize('disable', Event::class);

        $validated_data = $request->validate([
            'event_ids' => 'required|array',
            'event_ids.*' => 'integer'
        ]);

        $event_ids = $validated_data['event_ids'];

        try {
            $events = Event::whereIn('id', $event_ids);
            $events->update(['is_disabled' => true]);

            return response(null, 200);
        } catch (ModelNotFoundException $err) {
            if (is_array($err->getIds())) {
                return response()->json([
                    'message' => 'Events with ids "' . implode(', ', $err->getIds()) . '" were not found.',
                ], 404);
            } else {
                return response()->json([
                    'message' => 'Event with id "' . $err->getIds() . '" was not found.',
                ], 404);
            }
        }
    }

    /**
     * Enables a certain event
     */
    public function enableEvent(Request $request) {
        $this->authorize('enable', Event::class);

        $validated_data = $request->validate([
            'event_ids' => 'required|array',
            'event_ids.*' => 'integer'
        ]);

        $event_ids = $validated_data['event_ids'];

        try {
            $events = Event::whereIn('id', $event_ids);
            $events->update(['is_disabled' => false]);

            return response(null, 200);
        } catch (ModelNotFoundException $err) {
            if (is_array($err->getIds())) {
                return response()->json([
                    'message' => 'Events with ids "' . implode(', ', $err->getIds()) . '" were not found.',
                ], 404);
            } else {
                return response()->json([
                    'message' => 'Event with id "' . $err->getIds() . '" was not found.',
                ], 404);
            }
        }
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
     * Solves a certain issue
     */
    public function solveIssue(Request $request) {
        $this->authorize('solve', Issue::class);

        $validated_data = $request->validate([
            'issue_id' => 'required|integer',
            'creator_id' => 'required|integer',
            'solver_id' => 'required|integer',
            'content' => ''
        ]);

        $issue_id = $validated_data['issue_id'];
        $creator_id = $validated_data['creator_id'];
        $solver_id = $validated_data['solver_id'];
        $content = $validated_data['content'] ? $validated_data['content'] : "";

        try {
            // Solve the issue
            $issue = Issue::findOrFail($issue_id);
            $issue->is_solved = true;
            $issue->solver_id = $solver_id;
            $issue->save();

            // Create the notification
            $notification = new Notification;
            $notification->type = 'IssueNotification';
            $notification->issue_id = $issue_id;
            $notification->user_id = $creator_id;
            $notification->content = $content;
            $notification->save();

            return response(null, 200);
        } catch (ModelNotFoundException $err) {
            return response(null, 404);
        }
    }
}
