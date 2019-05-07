<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Event;
use App\EventCategory;

class HomepageController extends Controller
{

    const ITEMS_PER_PAGE = 5;

    public function display(Request $request) {

        $search_query = $request->get('search');
        $location_query = $request->get('location');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        $events = Event::relevant()
        ->when(!empty($search_query), function ($q) use ($search_query) {
            return Event::FTSScope($q, $search_query);
        })
        ->when(!empty($location_query), function ($q) use ($location_query) {
            return Event::LocationScope($q, $location_query);
        })
        ->paginate(HomepageController::ITEMS_PER_PAGE);

        $events->appends([
            'search' => $search_query,
            'location' => $location_query
        ]);

        $categories = EventCategory::all();

        return view('pages.homepage', 
            [
                'events' => $events,
                'categories' => $categories
            ]
        );
    }
}
