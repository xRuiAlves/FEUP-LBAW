<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Database\DB;

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
    public function __construct() {
        $this->middleware('auth', ['except' => ['show']]);
    }

    /**
     * Shows the event for a given id.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        $event = Event::find($id);

        if (is_null($event)) {
            return abort(404, 'The event with id ' . $id . ' does not seem to exist.');
        }

        // TODO: Decide which view to show based on auth probably
        // $this->authorize('show', $event); //TODO
        
        $owner = $event->owner;

        $category = $event->category;
                
        $announcements = $event->posts()->announcements()->get();

        $user = Auth::user();

        if($user) {
            $favorited = $event->favorited($id, $user->id);
        } else {
            $favorited = false;
        }

        $discussions = $event->posts()->discussions()->get();
        $discussion_comments = [];
        foreach($discussions as $i => $discussion) {
            $discussion_comments[$i] = $discussion->comments()->get();
        }

        return view('pages.events.index',
        [
            'event' => $event, 'owner' => $owner, 'announcements' => $announcements, 'category' => $category, 'favorited' => $favorited,
            'discussions' => $discussions, 'discussion_comments' => $discussion_comments
        ]);
    }

    /**
     * Renders the event creation page
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
        $this->authorize('create', Event::class);

        $categories = EventCategory::all();

        return view('pages.events.create', ['categories' => $categories]);
    }

    /**
     * Creates a new event.
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

        return redirect($event->href);
    }

    /**
     * Creates a new event category.
     */
    public function storeCategory(Request $request) {
        $this->authorize('createCategory', Event::class);

        $validated_data = $request->validate([
            'name' => 'required'
        ]);
        
        $name = $validated_data['name'];

        try {
            $event = new EventCategory();
            $event->name = $name;
            $event->save();

            return response()->json([
                'category_id' => $event->id
            ], 200);
        } catch (QueryException $err) {
            $err_msg = "";
            if ($err->getCode() == 23505) {
                $err_message = "There already exists a category with the '" . $name . "'.";
            } else if ($err->getCode() == 22001) {
                $err_message = "The category name must be, at most, 20 characters long.";
            }

            return response()->json([
                'message' => $err_message
            ], 400);
        }
    }

    /**
     * Renames an event category.
     */
    public function renameCategory(Request $request) {
        $this->authorize('renameCategory', Event::class);

        $validated_data = $request->validate([
            'id' => 'required',
            'name' => 'required'
        ]);
        
        $id = $validated_data['id'];
        $name = $validated_data['name'];

        try {
            $category = EventCategory::findOrFail($id);
            $category->name = $name;
            $category->save();

            return response()->json([], 200);
        } catch (ModelNotFoundException $err) {
            return response()->json([], 404);
        } catch (QueryException $err) {
            $err_msg = "";
            if ($err->getCode() == 23505) {
                $err_message = "There already exists a category with the '" . $name . "'.";
            } else if ($err->getCode() == 22001) {
                $err_message = "The new category name must be, at most, 20 characters long.";
            }

            return response()->json([
                'message' => $err_message
            ], 400);
        }
    }

    /**
     * Updates an existing event.
     * 
     * @return Event The updated event.
     */
    public function update(Request $request) {

    }

    public function delete(Request $request, $id) {
        // TODO
        // $card = Card::find($id);

        // $this->authorize('delete', $card);
        // $card->delete();

        // return $card;
    }   

    public function manage(Request $request){

        $event = Event::find($request->id);

        $this->authorize('eventSettings', $event);

        $attendees = $event->attendees()->paginate(10, ['*'], 'attendees');
        $attendees->setPageName('attendees');

        $organizers = $event->organizers()->paginate(10, ['*'], 'organizers');
        $organizers->setPageName('organizers');

        return view('pages.events.manage', 
        [
            'event' => $event,
            'attendees' => $attendees,
            'organizers' => $organizers,
            'isEventAdmin' => Auth::user()->id === $event->user_id
        ]);    
    }

    public function checkIn(Request $request){

        $event = Event::find($request->id);
        
        $this->authorize('eventSettings', $event);

        try{
            $ticket = $event->attendees->find($request->user_id)->ticket;
            $ticket->is_checked_in = true;
            $ticket->check_in_organizer_id = Auth::user()->id;
            $ticket->save();

            return response()->json([], 200);
        } catch (ModelNotFoundException $err) {
            return response()->json([], 404);
        }catch(QueryException $e){
            return response()->json([], 400);
        }
    }

    public function removeAttendee(Request $request){

        $request->validate([
            'user_id' => 'required|integer'
        ]);

        $event = Event::find($request->id);
        
        $this->authorize('eventSettings', $event);

        try{
            $ticket = $event->attendees->find($request->user_id)->ticket;
            $ticket->delete();

            return response()->json([], 200);
        } catch (ModelNotFoundException $err) {
            return response()->json([], 404);
        }catch(QueryException $e){
            return response()->json([], 400);
        }
    }

    public function removeOrganizer(Request $request){

        $request->validate([
            'user_id' => 'required|integer'
        ]);

        $event = Event::find($request->id);
        
        $this->authorize('eventAdmin', $event);

        if($event->user_id === Auth::user()->id){
            return response()->json(['Cannot remove event admin'], 400);
        }

        try{
            $pivot = $event->organizers->find($request->user_id)->pivot;
            $pivot->delete();

            return response()->json([], 200);
        } catch (ModelNotFoundException $err) {
            return response()->json([], 404);
        }catch(QueryException $e){
            return response()->json([], 400);
        }
    }
}
