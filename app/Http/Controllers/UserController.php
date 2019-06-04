<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\User;
use App\Event;
use App\Utilities\TimeUtilities;
use Hash;

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

    public function changeName(Request $request) {
        $this->authorize('updateName', User::class);

        $validated_data = $request->validate([
            'name' => 'required'
        ]);

        $name = $validated_data["name"];
        $id = auth()->user()->id;

        try {
            $user = User::find($id);
            $user->name = $name;    
            $user->save();
        } catch (ModelNotFoundException $err) {
            return response(null, 404);
        } catch (QueryException $err) {
            if ($err->getCode() == 22001 || $err->getCode() == 23514) {
                return response()->json([
                    'message' => 'The chosen name must be between 3 and 20 characters long.'
                ], 400);
            } else {
                return response()->json([
                    'message' => 'Bad request.'
                ], 400);
            }
        }
    }

    public function changePassword(Request $request){
        $validatedData = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string',
        ]);

        $current_password = $validatedData["current_password"];
        $new_password = $validatedData["new_password"];

        if (!(Hash::check($current_password, Auth::user()->password))) {
            return redirect()->back()->with(
                "error",
                "The current password you provided is not correct. Please try again."
            );
        }
        
        $user = Auth::user();
        $user->password = bcrypt($request->get('new_password'));
        $user->save();
        return redirect()->back()->with(
            "success",
            "Your password was successfully changed!"
        );
    }

    public function deleteAccount(Request $request) {
        User::destroy(auth()->user()->id);
        return redirect('/');
    }
}
