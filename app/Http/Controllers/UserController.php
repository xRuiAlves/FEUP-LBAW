<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

use App\User;
use App\Event;
use App\Utilities\TimeUtilities;

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
      // $user = User::find(2);

      $owned_events = $user->ownedEvents()->get()
      ->map(function ($event, $key) {
        $event['relationship'] = 'owner';
        return $event;
      });

      // return $owned_events;

      // Distinct because the user might have several tickets for the same event
      $attending_events = $user->attendingEvents()->distinct()->get()
      ->map(function ($event, $key) {
        $event['relationship'] = 'attendee';
        return $event;
      });

      // return $attending_events;
      
      $organizing_events = $user->organizingEvents()->get()
      ->map(function ($event, $key) {
        $event['relationship'] = 'organizer';
        return $event;
      });
      
      $favorite_events = $user->favoriteEvents()->get()
      ->map(function ($event, $key) {
        $event['relationship'] = 'favorite';
        return $event;
      });

      // return $organizing_events;

      // The order for merge must be this one because of not overriding positions with higher priority
      $events = $organizing_events->merge($attending_events)->merge($owned_events)->merge($favorite_events)->sortBy('start_timestamp')->values();

      // return $events;

      // TODO: Key the events array by month of the year so that the templating can do its magic :/

      return view('pages.dashboard', ['user' => $user, 'events' => $events]);
    }
}
