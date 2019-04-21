<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Event;
use App\User;
use App\Issue;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the admin dashboard for handling users.
     *
     * @return \Illuminate\Http\Response
     */
    public function users()
    {
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
    public function events()
    {
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
    public function issues()
    {
        if(!Auth::user()->is_admin) { // TODO: Change this to use policies
            return abort(401, 'You do not possess the required permissions to acces the administration pages');
        }
        return view('pages.admin.issues', ['issues' => Issue::paginate(10)]);
    }
}
