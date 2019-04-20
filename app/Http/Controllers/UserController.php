<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\User;

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

      $events = $user->ownedEvents()
                  // EXTRACT(YEAR FROM start_timestamp) as year, 
                  ->select(DB::raw('EXTRACT(YEAR FROM start_timestamp) as year, EXTRACT(DOW FROM start_timestamp) as day_of_week,
                                  EXTRACT(MONTH FROM start_timestamp) as month, EXTRACT(DAY FROM start_timestamp) as day,
                                  EXTRACT(HOUR FROM start_timestamp) as hour, EXTRACT(MINUTE FROM start_timestamp) as minute'),
                          'id', 'title', 'location', 'user_id')
                  ->groupBy('month', 'start_timestamp', 'events.id')
                  ->orderBy('month', 'asc', 'day', 'asc')
                  ->get();

        // return $events;

        // TODO: Add getting the events that the user is attending
        // TODO: Key the events array by month of the year so that the templating can do its magic :/

        return view('pages.user_dashboard', ['user' => $user, 'events' => $events]);
      }
}
