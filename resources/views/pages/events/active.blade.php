@extends('layouts.app')

@section('asset_includes')
<link href="{{ asset('css/event_page.css') }}" rel="stylesheet">
@endsection

@section('open-graph-title')
<meta property="og:title" content="{{$event->title}}">
@endsection

@section('title', $event->title . ' - Eventually')

@section('content')
    <div id="background_wave"></div>

<div id="page-card" class="container card-container font-content event-card" data-event-id={{$event->id}}>
    <div class="event-brief">
        <div class="row no-gutters main">
            <div class="col-12 col-lg-9 event-title font-title">
                <h1>
                    {{$event->title}}
                    @if(Auth::check())
                        @if($favorited)
                            <button title="Unmark as Favorite" type="button" class="btn btn-primary active" id="favorite-marker" data-event-id="{{$event->id}}">
                        @else        
                            <button title="Mark as Favorite" type="button" class="btn btn-primary" id="favorite-marker" data-event-id="{{$event->id}}">
                        @endif
                                <span  class="favorite"><i class="fas fa-star"></i></span>
                            </button>
                    @endif                    
                </h1>
            </div>
            <div class="col-12 col-lg-3 attend-btn alone-right">
                @if($event->organizers()->get()->contains(Auth::user()))
                    <button type="button" class="btn" id="attend-btn" disabled data-event-id="{{$event->id}}" title="You are an Organizer">
                @else
                    <button type="button" class="btn" id="attend-btn" data-event-id="{{$event->id}}">
                @endif
                    <span>
                        <i class="fas fa-calendar-check icon-left"></i>
                    </span>
                    Attend
                </button>
                @if($is_organizer)
                    <a href="/event/{{$event->id}}/manage" class="btn">
                        <span>
                            <i class="fas fa-edit icon-left"></i>
                        </span>
                        Manage Event
                    </a>
                    <a href="/event/{{$event->id}}/edit" class="btn">
                        <span>
                            <i class="fas fa-edit icon-left"></i>
                        </span>
                        Edit Information
                    </a>
                @endif
            </div>
            <div class="col-12 hosted-by-label mt-2 mt-lg-0">
                <h6>
                    Event hosted by {{$owner->name}}
                    @if($owner->is_disabled)
                        <p class="user-account-disabled"><strong>Note:</strong> This user's account was disabled</p>
                    @endif
                </h6>
            </div>
            <div class="col-12 attendance-label mt-2 mt-lg-0">
                <h6 title="Event Attendance">
                    <i class="fas fa-user mr-2"></i>
                    {{$event->attendees()->count()}}
                    @if($event->capacity != -1)
                    / {{$event->capacity}}
                    @endif
                </h6>
            </div>
            @if($event->attendees()->get()->contains(Auth::user()))
            <div class="col-12 tickets-label mt-2 mt-lg-0">
                <h6>
                    You already have {{$event->attendees()->where('user_id', Auth::user()->id)->count()}} tickets for this event.
                    <a href="/event/{{$event->id}}/tickets">Manage Tickets</a>
                </h6>
            </div>
            @endif
        </div>
        <div class="mobile-wave" id="background_wave"></div>
        <div class="separator main-separator">
            <hr>
        </div>
        <div class="row event-details font-title">
            <div class="col-6 event-spacetime">
                <div class="row mb-1 no-gutters date">
                    <div class="col-12 col-md-auto">
                        <span>
                            <i class="far fa-calendar-alt icon-left"></i>
                        </span> {{$event->formatted_start_timestamp}}
                    </div>
                    @if(!empty($event->end_timestamp))
                    <div class="col-12 col-md-auto">
                        <span>
                            <i class="fas fa-minus icon-left"></i>
                        </span> {{$event->formatted_end_timestamp}}
                    </div>
                    @endif
                </div>
                @if(!empty($event->location))
                <div class="row">
                    <div class="col-12">
                        <span>
                            <i class="fas fa-map-marker-alt icon-left"></i>
                        </span> {{$event->location}}
                    </div>
                </div>
                @endif
            </div>
            <div class="col-6 event-category">
                <div class="row mb-1">
                    <div class="col-12">
                        @if($event->price == 0)
                        Free
                        @else
                        {{$event->price}} €
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        {{$category->name}}
                        <span>
                            <i class="fas fa-tag icon-right"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row event-description">
            <div class="col-12">
                <p>{{$event->description}}</p>
            </div>
        </div>
        <div class="separator">
            <hr>
        </div>
    </div>
    <div class="row no-gutters event-map">
        <div class="col-12">
            <iframe class="event-map"
                src="https://maps.google.com/?q={{$event->latitude}},{{$event->longitude}}&amp;ie=UTF8&amp;t=&amp;z=14&amp;iwloc=B&amp;output=embed">
            </iframe>
        </div>
    </div>
    @include('pages.events.forum')
</div>

<div id="attend-event-modal" class="modal fade font-content" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title custom-modal-title">Ticket</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Do you wish to obtain a free ticket to Semana de Informática 2019?
            </div>
            <div class="modal-footer">
                <a href="/event_page_attendee.html" class="btn publish-button">Confirm</a>
                <button type="button" class="btn btn-secondary close-button" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <script src="{{ asset('js/active_events.js') }}" defer></script>
@endsection