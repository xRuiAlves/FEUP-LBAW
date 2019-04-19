<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\UserDashboard;

class UserDashboardController extends Controller
{
    /**
     * Shows the home
     *
     * @return Response
     */
    public function show() {
      return view('pages.user_dashboard');
    }
}
