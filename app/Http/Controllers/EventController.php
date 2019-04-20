<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Event;
use App\User;

class EventController extends Controller
{
    /**
     * Shows the event for a given id.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        $event = Event::where('id', $id)
                        ->select('id', 'title', 'description', 'price', 'location', 'latitude', 'longitude', 'start_timestamp', 'end_timestamp', 'status')
                        ->first();

        // $this->authorize('show', $event); //TODO

        // $owner = User::find(1)->events()->get();
        // return $owner;

        return view('pages.events.index', ['event' => $event]);
    }

    /**
     * Lists all events. Not sure if should keep this or not <------------
     *
     * @return Response
     */
    public function list() {
        // TODO
        // if (!Auth::check()) return redirect('/login');
        // $this->authorize('list', Card::class);

        // $events = Auth::user()->cards()->orderBy('id')->get();

        // return view('pages.cards', ['cards' => $cards]);

        // Temporary debug
        return Event::select()->get();
    }

    /**
     * Creates a new evebt.
     *
     * @return Event The event created.
     */
    public function create(Request $request) {
        // TODO
        // $card = new Card();

        // $this->authorize('create', $card);

        // $card->name = $request->input('name');
        // $card->user_id = Auth::user()->id;
        // $card->save();

        // return $card;
    }

    public function delete(Request $request, $id) {
        // TODO
        // $card = Card::find($id);

        // $this->authorize('delete', $card);
        // $card->delete();

        // return $card;
    }
}
