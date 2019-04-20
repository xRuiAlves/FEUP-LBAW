<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Event;
use App\Post;

class EventController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['show']);
    }

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

        // TODO: Decide which view to show based on auth probably
        // $this->authorize('show', $event); //TODO
        
        $owner = $event->owner;

        $category = $event->category;
                
        $announcements = $event->posts()->announcements()->get();

        $discussions = $event->posts()->discussions()->get();
        $discussion_comments = [];
        foreach($discussions as $i => $discussion) {
            $discussion_comments[$i] = $discussion->comments()->get();
        }

        return view('pages.events.index',
        [
            'event' => $event, 'owner' => $owner, 'announcements' => $announcements, 'category' => $category,
            'discussions' => $discussions, 'discussion_comments' => $discussion_comments
        ]);
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
     * Renders the event creation page
     *
     */
    public function create(Request $request) {
        // TODO
        // $card = new Card();

        // $this->authorize('create', $card);

        // $card->name = $request->input('name');
        // $card->user_id = Auth::user()->id;
        // $card->save();

        // return $card;
        $this->authorize('create', Event::class);

        return view('pages.events.create');
    }

    /**
     * Renders the event creation page
     *
     * @return Event The event created.
     */
    public function store(Request $request) {
        $event = new Event();
        
        $this->authorize('create', $event);

        echo "was authorized";
        $this->validate($request, [
            'title' => 'required',
            'location' => 'required',
            'price' => 'required',
            // 'category' => 'required',
            // 'start_timestamp' => 'required',
            'description' => 'required',
        ]);
        
        echo "was validated";

        $event->title = $request->input('title');
        $event->location = $request->input('location');
        $event->price = $request->input('price');
        // $event->category = $request->input('category');
        // $event->start_timestamp = $request->input('start_timestamp');
        $event->start_timestamp = date('Y-m-d H:i:s', time() + 2400);
        $event->event_category_id = 1;
        $event->description = $request->input('description');

        $event->user_id = auth()->user()->id;
        $event->save();

        echo "was saved";
        
        return redirect('/event/'.$event->id);
    }

    public function delete(Request $request, $id) {
        // TODO
        // $card = Card::find($id);

        // $this->authorize('delete', $card);
        // $card->delete();

        // return $card;
    }
}
