<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Home;

class HomeController extends Controller
{
    /**
     * Shows the home
     *
     * @return Response
     */
    public function show() {
      return view('pages.home');
    }
}
