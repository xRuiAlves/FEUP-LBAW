<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function show() {
        if (!Auth::check()) {
            return redirect('/login');
        } else {
            return view('pages.settings');
        }
    }
}
