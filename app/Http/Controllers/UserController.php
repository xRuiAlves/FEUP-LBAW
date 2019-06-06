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
use App\FavoritEventEntry;
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

        $user = Auth::user();

        // Distinct because the user might have several tickets for the same event
        $attending_events = $user->attendingEvents()->distinct()->get()
        ->map(function ($event, $key) {
            $event['relationship'] = 'attendee';
            return $event;
        });

        $organizing_events = $user->organizingEvents()->get()
        ->map(function ($event, $key) {
            $event['relationship'] = 'organizer';
            return $event;
        });

        

        $favorite_events = $user->favoriteEvents()->get()
        ->map(function ($event, $key) {
            $event['is_favorite'] = true;
            return $event;
        });

        // The order for merge must be this one because of not overriding positions with higher priority
        $events = $organizing_events->merge($attending_events);

        foreach ($events as $key => $value) {
            if($favorite_events->contains($events[$key])) {
                $events[$key]['is_favorite'] = true;
            }
        }

        $retEvents = $favorite_events->merge($events)->sortBy('start_timestamp')->values();

        // TODO: Key the events array by month of the year so that the templating can do its magic :/

        return view('pages.dashboard', ['user' => $user, 'events' => $retEvents]);
    }

    public function changeName(Request $request) {
        $this->authorize('updateName', User::class);

        $validated_data = $request->validate([
            'name' => 'required'
        ]);

        $name = $validated_data["name"];
        $id = auth()->user()->id;

        try {
            $user = User::findOrFail($id);
            $user->name = $name;    
            $user->save();
            return response()->json([], 200);
        } catch (ModelNotFoundException $err) {
            return response()->json([], 404);
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

    public function markEventAsFavorite(Request $request) {
        $this->authorize('markFavorite', User::class);

        $validated_data = $request->validate([
            'event_id' => 'required'
        ]);

        try {
            $event_id = $request->event_id;
            $event = Event::findOrFail($event_id);

            $event->usersFavorited()->attach(Auth::user());
            $event->save();

            return response()->json([
                'favorited' => true,
            ]);

        } catch (ModelNotFoundException $err) {
            return response()->json([], 404);
        } catch (QueryException $err) {
            return response()->json([
                'message' => 'Bad request.'
            ], 400);
            
        }
    }

    public function unmarkEventAsFavorite(Request $request) {
        $this->authorize('markFavorite', User::class);

        $validated_data = $request->validate([
            'event_id' => 'required'
        ]);

        try {

            $event_id = $request->event_id;
            $event = Event::find($event_id);

            $event->usersFavorited()->detach(Auth::user());
            $event->save();

            return response()->json([
                'favorited' => false,
            ], 200);


        } catch (ModelNotFoundException $err) {
            return response()->json([], 404);
        } catch (QueryException $err) {
            return response()->json([
                'message' => 'Bad request.'
            ], 400);
            
        }
    }

    public function promoteToAdmin(Request $request) {
        $this->authorize('promoteToAdmin', User::class);

        $validated_data = $request->validate([
            'user_id' => 'required'
        ]);

        $user_id = $validated_data["user_id"];

        try {
            $user = User::findOrFail($user_id);
            $user->is_admin = true;    
            $user->save();
            return response()->json([], 200);
        } catch (ModelNotFoundException $err) {
            return response()->json([], 404);
        } 
    }

    public function enable(Request $request) {
        $this->authorize('enableDisable', User::class);

        $validated_data = $request->validate([
            'user_id' => 'required'
        ]);

        $user_id = $validated_data["user_id"];

        try {
            $user = User::findOrFail($user_id);
            $user->is_disabled = false;    
            $user->save();
            return response()->json([], 200);
        } catch (ModelNotFoundException $err) {
            return response()->json([], 404);
        } 
    }

    public function disable(Request $request) {
        $this->authorize('enableDisable', User::class);

        $validated_data = $request->validate([
            'user_id' => 'required'
        ]);

        $user_id = $validated_data["user_id"];

        try {
            $user = User::findOrFail($user_id);
            $user->is_disabled = true;    
            $user->save();

            $user_events = Event::ownerUser($user_id)->enabled()->get();
            $disabled_events = [];
            foreach($user_events as $event) {
                $event->is_disabled = true;
                $event->save();
                array_push($disabled_events, $event->title);
            }

            return response()->json([
                "disabled_events" => $disabled_events
            ], 200);
        } catch (ModelNotFoundException $err) {
            return response()->json([], 404);
        } 
    }
}
