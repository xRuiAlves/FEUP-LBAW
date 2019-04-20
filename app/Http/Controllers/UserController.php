<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

use App\User;
use App\Event;

class UserController extends Controller
{
    /**
     * Shows the User's Dashboard
     *
     * @return Response
     */
    public function showDashboard() {
      if (!Auth::check()) {
        return redirect('/login');
      }

      // TODO - Must add EventPolicy as well
      // $this->authorize('list', Event::class);

      $user = Auth::user();

      $owned_events = User::find(2)->ownedEvents()
                  // EXTRACT(YEAR FROM start_timestamp) as year, 
                  ->select(DB::raw('EXTRACT(YEAR FROM start_timestamp) as year, EXTRACT(DOW FROM start_timestamp) as day_of_week,
                                  EXTRACT(MONTH FROM start_timestamp) as month, EXTRACT(DAY FROM start_timestamp) as day,
                                  EXTRACT(HOUR FROM start_timestamp) as hour, EXTRACT(MINUTE FROM start_timestamp) as minute'),
                          'id as event_id', 'user_id', 'title', 'location')
                  ->orderBy('month', 'asc', 'day', 'asc')
                  ->get();

      // TODO: Missing getting the event id

      $attending_events = User::find(2)->attendingEvents()
                          ->select(DB::raw('EXTRACT(YEAR FROM start_timestamp) as year, EXTRACT(DOW FROM start_timestamp) as day_of_week,
                                            EXTRACT(MONTH FROM start_timestamp) as month, EXTRACT(DAY FROM start_timestamp) as day,
                                            EXTRACT(HOUR FROM start_timestamp) as hour, EXTRACT(MINUTE FROM start_timestamp) as minute'),
                                    'event_id', 'title', 'location')
                          ->orderBy('month', 'asc', 'day', 'asc')
                          ->get();

      // TODO Fix returning dupliates somehow :/
      // return $attending_events;
      // return $owned_events;
      $events = $owned_events->merge($attending_events)->groupBy('month');

      // TODO: Add getting the events that the user is attending
      // $attending_events = ['id' => 'test'];

      return $events;

      // TODO: Key the events array by month of the year so that the templating can do its magic :/

      return view('pages.user_dashboard', ['user' => $user, 'events' => $events]);
    }
}
