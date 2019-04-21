<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


use App\Event;
use App\EventCategory;
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

        $categories = EventCategory::all();

        return view('pages.events.create', ['categories' => $categories]);
    }

    /**
     * Renders the event creation page
     *
     * @return Event The event created.
     */
    public function store(Request $request) {
        
        $this->authorize('create', Event::class);
        

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'location' => 'required',
            'price' => 'required|numeric|min:0',
            'event_category_id' => 'required',
            'start_timestamp' => 'required|date|after:now',
            'end_timestamp' => 'sometimes|nullable|date|after:start_date',
            'description' => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect('event/create')
            ->withErrors($validator)
            ->withInput();
        }
         
        $event = new Event();
        $event->title = $request->input('title');
        $event->location = $request->input('location');
        $event->price = $request->input('price');
        $event->event_category_id = $request->input('event_category_id');
        $event->start_timestamp = $request->input('start_timestamp');
        $event->description = $request->input('description');

        if (!empty(request()->input('end_timestamp'))) {
            $event->end_timestamp = $request->input('end_timestamp');
        }

        $event->user_id = auth()->user()->id;
        $event->save();

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
