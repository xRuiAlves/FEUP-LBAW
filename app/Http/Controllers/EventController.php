<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Event;
use App\Post;

class EventController extends Controller
{
    /**
     * Shows the event for a given id.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        // $event = Event::where('id', $id)
        //                 ->select('id', 'title', 'description', 'price', 'location', 'latitude', 'longitude', 'start_timestamp', 'end_timestamp', 'status')
        //                 ->first();

        $event = Event::find($id);

        
        // $this->authorize('show', $event); //TODO
        
        $owner = $event->owner()->get();
        
        $announcements = $event->posts()->announcements()->get();
        
        $discussions = $event->posts()->discussions()->get();
        
        // TODO - Methods defined but not working for some reason... ORMs we meet again...
        // $discussion_comments = $discussions->comments()->get();
        // return $event->posts()->comments()->get();
        // return $discussion_comments;

        return view('pages.events.index', ['event' => $event, 'owner' => $owner, 'announcements' => $announcements, 'discussions' => $discussions]);
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
