@extends('layouts.app')

@section('asset_includes')
<link href="{{ asset('css/event_page.css') }}" rel="stylesheet">
@endsection

@section('title', $event->title . ' - Eventually')

@section('content')
<div id="background_wave"></div>

<div id="page-card" class="container card-container font-content event-card" data-event-id={{$event->id}}>
    <div class="event-brief">
        <div class="row no-gutters main">
            <div class="col-12 event-title font-title">
                    <h1>{{$event->title}}</h1>
            </div>
        </div>
        <div class="mobile-wave" id="background_wave"></div>
        <div class="event-disabled-body">
            <p>
                This event has been disabled by the application administrators.
            </p>
            <p>
                To search for more events click
                <a href="/#search-box-anchor">here</a>.
            </p>
        </div>
    </div>
</div>
@endsection