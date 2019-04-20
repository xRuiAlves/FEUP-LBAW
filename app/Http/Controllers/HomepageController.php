<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Event;

class HomepageController extends Controller
{
    public function display() {

        $relevantEvents = Event::relevant()->paginate(5);

        return view('pages.homepage', 
            ['events' => $relevantEvents]);
    }
}
