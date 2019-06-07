<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Event;
use App\EventCategory;
use App\Post;
use App\User;
use App\Notification;
use App\EventVoucher;


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

        $owner = $event->owner;

        $category = $event->category;
                
        $announcements = $event
            ->posts()
            ->announcements()
            ->orderBy('timestamp', 'desc')
            ->paginate(10, ["*"], "announcements");

        $user = Auth::user();

        if($user) {
            $favorited = $event->favorited($id, $user->id);
        } else {
            $favorited = false;
        }

        $discussions = $event
            ->posts()
            ->discussions()
            ->orderBy('rating', 'desc')
            ->orderBy('timestamp', 'desc')
            ->paginate(10, ["*"], "discussions");
        $discussion_comments = [];
        foreach($discussions as $i => $discussion) {
            $discussion_comments[$i] = $discussion->comments()->get();
        }

        $is_organizer = Auth::check() ? $event->organizers()->where('user_id', Auth::user()->id)->exists() : false;

        return view('pages.events.index',
        [
            'event' => $event, 'owner' => $owner, 'announcements' => $announcements, 'category' => $category, 'favorited' => $favorited,
            'discussions' => $discussions, 'discussion_comments' => $discussion_comments, 'is_organizer' => $is_organizer
        ]);
    }

    /**
     * Renders the event creation page
     *
     * @return Event The event created.
     */
    public function create(Request $request) {
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
            'title' => 'required|max:30|min:1',
            'location' => 'required_with:longitude,latitude|string|nullable|max:80',
            'price' => 'required|numeric|min:0',
            'event_category_id' => 'required|integer',
            'start_timestamp' => 'required|date|after:now',
            'end_timestamp' => 'sometimes|nullable|date|after:start_timestamp',
            'description' => 'required|string',
            'latitude' => 'required_with:location,longitude|nullable|numeric',
            'longitude' => 'required_with:location,latitude|nullable|numeric',
        ]);
        
        if ($validator->fails()) {
            return redirect('event/create')
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
         
        try {
            $event = new Event();
            $event->title = $request->input('title');
            $event->location = $request->input('location');
            $event->latitude = $request->input('latitude');
            $event->longitude = $request->input('longitude');
            $event->price = $request->input('price');
            $event->event_category_id = $request->input('event_category_id');
            $event->start_timestamp = $request->input('start_timestamp');
            $event->description = $request->input('description');

            if (!empty($request->input('end_timestamp'))) {
                $event->end_timestamp = $request->input('end_timestamp');
            }

            if (!empty($request->input('latitude')) && !empty($request->input('longitude'))) {
            }

            $event->user_id = auth()->user()->id;
            $event->save();

            DB::table('organizers')->insert([
                'user_id' => $event->user_id, 
                'event_id' => $event->id
            ]);

            DB::commit();

            return redirect($event->href);
        } catch (QueryException $err) {
            DB::rollBack();
            return redirect('event/create')
                ->withErrors(["Error in submitting request to database"])
                ->withInput();
        }
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

    public function delete(Request $request) {

        $event = Event::find($request->id);
        
        $this->authorize('eventAdmin', $event);
        
        try{
            $event->is_cancelled = true;
            $event->save();

            return response()->json([], 200);
        } catch (ModelNotFoundException $err) {
            return response()->json([], 404);
        }catch(QueryException $e){
            return response()->json([], 400);
        }
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

        if($event->user_id === $request->user_id){
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

    public function quitOrganization(Request $request){
        $event = Event::find($request->id);
        
        $this->authorize('eventSettings', $event);

        if($event->user_id === Auth::user()->id){
            return response()->json(['Cannot remove event admin'], 400);
        }

        try{
            $pivot = $event->organizers->find(Auth::user()->id)->pivot;
            $pivot->delete();

            return response()->json([], 200);
        } catch (ModelNotFoundException $err) {
            return response()->json([], 404);
        }catch(QueryException $e){
            return response()->json([], 400);
        }
    }

    public function addOrganizerPage(Request $request){

        $event = Event::find($request->id);

        $this->authorize('eventAdmin', $event);

        $search_query = $request->get('search');
        
        if(!empty($search_query)){
            $users = User::FTS($search_query)->whereNotIn('id', $event->organizers->pluck('id'))->paginate(AdminController::ITEMS_PER_PAGE);

            $users->withPath('?search='.$search_query);
        }else{
            $users = User::whereNotIn('id', $event->organizers->pluck('id'))->paginate(AdminController::ITEMS_PER_PAGE);
        }
        
        return view('pages.events.add-organizer', ['event' => $event, 'users' => $users, 'searchQuery' => $search_query]);
    }



    public function invitePage(Request $request){

        $event = Event::find($request->id);

        $this->authorize('eventSettings', $event);

        $search_query = $request->get('search');
        
        if(!empty($search_query)){
            $users = User::FTS($search_query)
                ->whereNotIn('id', $event->attendees->pluck('id'))
                ->whereNotIn('id', Notification::where('event_id', $event->id)->invitedEvents()->pluck('user_id'))
                ->paginate(AdminController::ITEMS_PER_PAGE);

            $users->withPath('?search='.$search_query);
        }else{
            $users = User::whereNotIn('id', $event->attendees->pluck('id'))
                ->whereNotIn('id', Notification::where('event_id', $event->id)->invitedEvents()->pluck('user_id'))
                ->paginate(AdminController::ITEMS_PER_PAGE);
        }

        return view('pages.events.invite', ['event' => $event, 'users' => $users, 'searchQuery' => $search_query]);
    }


    public function addOrganizer(Request $request){
        $request->validate([
            'user_id' => 'required|integer'
        ]);

        $event = Event::find($request->id);
        
        $this->authorize('eventAdmin', $event);

        try{
            $event->organizers()->attach($request->user_id);

            return response()->json([], 200);
        } catch (ModelNotFoundException $err) {
            return response()->json([], 404);
        }catch(QueryException $e){
            return response()->json([], 400);
        }
    }

    public function invite(Request $request){

        $request->validate([
            'user_id' => 'required|integer'
        ]);

        $event = Event::find($request->id);

        $this->authorize('eventSettings', $event);

        try{
            // Create the notification
            $notification = new Notification;
            $notification->type = 'EventInvitation';
            $notification->user_id = $request->user_id;
            $notification->event_id = $event->id;
            $notification->save();
        
            return response()->json([], 200);
        } catch (ModelNotFoundException $err) {
            return response()->json([], 404);
        }catch(QueryException $e){
            return response()->json([], 400);
        }
    }

    public function generateVouchersPage(Request $request){

        $event = Event::find($request->id);

        $this->authorize('eventSettings', $event);

        return view('pages.events.generate-vouchers', ['event' => $event]);
    }

    public function generateVouchers(Request $request){
        $validated_data = $request->validate([
            'nVouchers' => 'required|integer|min:1',
        ]);

        $nVouchers = $validated_data['nVouchers'];
        $event_id = $request->id;

        $this->authorize('eventSettings', Event::find($event_id));

        $result = [];

        try {
            for($i = 0; $i < $nVouchers; $i++){
                $newCode;
                do {
                    $newCode = "EVT-" . Str::uuid()->toString();
                } while(EventVoucher::where('code', $newCode)->exists());

                $newVoucher = new EventVoucher();
                $newVoucher->event_id = $event_id;
                $newVoucher->user_id = Auth::user()->id;
                $newVoucher->code = $newCode;
                $newVoucher->save();

                array_push($result, $newCode);
            }

            return response()->json($result, 200);
        } catch(QueryException $e) {
            return response()->json([], 500);
        }
    }
}
