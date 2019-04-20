<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        return view('pages.admin.users', ['users' => User::paginate(10)]);
    }

    /**
     * Show the admin dashboard for handling events.
     *
     * @return \Illuminate\Http\Response
     */
    public function events()
    {
        return view('pages.admin.events', ['events' => Event::paginate(10)]);
    }

    /**
     * Show the admin dashboard for handling events.
     *
     * @return \Illuminate\Http\Response
     */
    public function issues()
    {
        return view('pages.admin.issues', ['issues' => Issue::paginate(10)]);
    }
}
