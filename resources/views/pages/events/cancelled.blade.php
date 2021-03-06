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
            <div class="col-12 cancelled-label">
                <h6>This event was cancelled</h6>
            </div>
            <div class="col-12 col-lg-9 event-title font-title">
                <h1>{{$event->title}}</h1>
            </div>
            <div class="col-12 col-lg-3 attend-btn alone-right">                
                <button disabled type="button" class="btn" data-toggle="modal" data-target="#attend-event-modal">
                    <span>
                        <i class="fas fa-calendar-check icon-left"></i>
                    </span>
                    Attend
                </button>
            </div>
            <div class="col-12 hosted-by-label mt-2 mt-lg-0">
                <h6>Event hosted by {{$owner->name}}</h6>
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

@endsection