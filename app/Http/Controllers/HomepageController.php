<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Event;
use App\EventCategory;

use Illuminate\Support\Facades\DB;


class HomepageController extends Controller
{

    const ITEMS_PER_PAGE = 5;

    public function display(Request $request) {

        $search_query = $request->get('search');
        $location_query = $request->get('location');
        $category = $request->get('event_category');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        $events = Event::relevant()
        ->when(!empty($search_query), function ($q) use ($search_query) {
            return Event::FTSScope($q, $search_query);
        })
        ->when(!empty($location_query), function ($q) use ($location_query) {
            return Event::LocationScope($q, $location_query);
        })
        ->when(!empty($category), function ($q) use ($category) {
            return Event::CategoryScope($q, $category);
        })
        ->when(!empty($start_date), function ($q) use ($start_date) {
            return Event::StartScope($q, new \DateTime($start_date));
        })
        ->when(!empty($end_date), function ($q) use ($end_date) {
            return Event::EndScope($q, new \DateTime($end_date));
        })
        ->paginate(HomepageController::ITEMS_PER_PAGE); 

        $events->appends([
            'search' => $search_query,
            'location' => $location_query,
            'category' => $category,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);

        $categories = EventCategory::all();


        // return $events;
        return view('pages.homepage', 
            [
                'events' => $events,
                'categories' => $categories
            ]
        );
    }
}
