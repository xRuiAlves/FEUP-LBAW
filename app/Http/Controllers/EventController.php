<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Event;
use App\EventCategory;
use App\Post;
use App\User;
use App\Notification;
use App\EventVoucher;
use App\Tag;
use App\Rating;


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
        $discussion_votes = [];
        foreach($discussions as $i => $discussion) {
            $discussion_comments[$i] = $discussion->comments()->get();
            if(Auth::user() && Rating::where('user_id',$user->id)->where('post_id', $discussion->id)->exists()) {
                $value = Rating::where('user_id', $user->id)->where('post_id', $discussion->id)->first()->value;
                $discussion_votes[$i] = $value;
            } else {
                $discussion_votes[$i] = null;
            }
        }

        $is_organizer = Auth::check() ? $event->organizers()->where('user_id', Auth::user()->id)->exists() : false;

        return view('pages.events.index',
        [
            'event' => $event, 'owner' => $owner, 'announcements' => $announcements, 'category' => $category, 'favorited' => $favorited,
            'discussions' => $discussions, 'discussion_comments' => $discussion_comments, 'is_organizer' => $is_organizer, 'discussion_votes' => $discussion_votes
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
     * Renders the event editing page
     */
    public function edit(Request $request) {
        
        $event = Event::find($request->id);
        $this->authorize('eventSettings', $event);
        
        $categories = EventCategory::all();
        
        return view('pages.events.create', ['categories' => $categories, 'event' => $event]);
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
            'capacity' => 'sometimes|nullable|integer|min:1',
        ]);
        
        if ($validator->fails()) {
            return redirect('event/create')
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
         
        try {
            if(!empty($request->id)){ //editing event
                $event = Event::find($request->id);
                $this->authorize('eventSettings', $event);
            }else{ //creating event
                $event = new Event();
                $event->user_id = auth()->user()->id;
            }
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

            if (!empty($request->input('capacity'))) {
                $event->capacity = $request->input('capacity');
            }else{
                $event->capacity = -1; //for when editing
            }
            
            $event->save();

            $tags = json_decode($request->tags);
            foreach($tags as $tagName){
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                
                if(!$event->tags()->where('id', $tag->id)->exists()){
                    $event->tags()->attach($tag->id);
                }
            }

            if(!empty($request->id)){ //editing event
                DB::commit();
                return redirect($event->href);
            }
            
            DB::table('organizers')->insert([
                'user_id' => $event->user_id, 
                'event_id' => $event->id
                ]);
                
            DB::commit();

            return redirect($event->href);
        } catch (QueryException $err) {
            DB::rollBack();
            return redirect(empty($request->id) ? 'event/create' : ('event/'.$event->id.'/edit'))
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

        try {

        
            $event = Event::findOrFail($event_id);
            $this->authorize('eventSettings', $event);

            $result = [];

            $current_num_attendees = EventVoucher::where('event_id', $event_id)->count();

            if($event->capacity != -1 && $current_num_attendees + $nVouchers > $event->capacity) { 
                // surpassed capacity
                return response()->json(['message' => 'Voucher limit exceeded for this request'], 400);
            }

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
        } catch(ModelNotFoundException $e) {
            return response()->json([], 404);
        }
    }


    public function showAttendPage(Request $request) {
        try {
            $event = Event::findOrFail($request->id);
            $this->authorize('attend', $event);

            return view('pages.events.attend', ['event' => $event]);
        } catch (ModelNotFoundException $e) {
            return redirect('/')->withErrors(['Event not available']);
        }
    }

    public function attend(Request $request, $event_id) {
        
        $request->validate([
            'tickets.*.nif' => 'required|numeric|digits:9',
            'tickets.*.address' => 'required|max:128',
            'tickets.*.billing_name' => 'required|max:64',
            'tickets.*.voucher_code' => 'required',
        ]);
       

        try {
            $event = Event::findOrFail($event_id);


            $current_num_attendees = $event->attendees()->count();

            if($event->capacity != -1 && $current_num_attendees + count($request->tickets) > $event->capacity) { //max capacity reached, cant buy so many tickets
                $num_tickets_to_buy = count($request->tickets);
                
                return response()->json([
                    'errors' => [
                        'global' => [
                            "Cannot buy $num_tickets_to_buy ticket(s). Attendance limit reached. Purchase halted.",
                        ]
                    ]
                ], 400);
            }
            
            
            for ($i=0; $i < count($request->tickets); $i++) { 
                $ticket = $request->tickets[$i];
            
                $ticket_info = [
                    'nif' => $ticket['nif'],
                    'billing_name' => $ticket['billing_name'],
                    'address' => $ticket['address'],
                ];

                if(!empty($ticket['voucher_code'])) { //using voucher code
                    $ticket_info['type'] = 'Voucher';
                    $ticket_num = $i + 1;

                    try {
                        $voucher = EventVoucher::where('code', '=', $ticket['voucher_code'])->firstOrFail();
                        $ticket_info['event_voucher_id'] = $voucher->id;

                        if($voucher->is_used) {//voucher already redeemed
                            return response()->json([
                                'errors' => [
                                    'global' => [
                                        "The submitted voucher for Ticket #$ticket_num was already used. Purchase halted.",
                                    ]
                                ]
                            ], 400);
                        }

                    } catch (ModelNotFoundException $e) {
                        
                        return response()->json([
                            'errors' => [
                                'global' => [
                                    "The submitted voucher for Ticket #$ticket_num is invalid. Purchase halted.",
                                ]
                            ]
                        ], 400);
                               
                    }

                } else {
                    $ticket_info['type'] = 'Paypal';
                    $ticket_info['paypal_order_id'] = 'PAYPAL-CONFIRMATION-DUMMY';
                }

                $event->attendees()->attach(Auth::user(), $ticket_info);                
            }

            

            return response()->json([
                'tickets' => $request->tickets,
                'num_attendees' => $event->attendees()->count(),
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'errors' => [
                    'global' => [
                        'The event was not found. Purchase halted.',
                    ]
                ]
            ], 400);
        }
    }
}
