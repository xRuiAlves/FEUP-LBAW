@extends('layouts.app')

@section('asset_includes')
<link href="{{ asset('css/event_page.css') }}" rel="stylesheet">
<link href="{{ asset('css/event_management.css') }}" rel="stylesheet">
@endsection

@section('scripts')
<script src="{{ asset('js/add_organizer.js')}}" defer></script>
@endsection

@section('title', 'Add Organizer - ' . $event->title . ' - Eventually')

@section('content')
<div id="background_wave"></div>

<div id="page-card" class="container card-container font-content event-card" data-event_id="{{$event->id}}">
    <div class="row no-gutters main">
        <div class="col-12 event-title font-title">
                Add Organizer - {{$event->title}}
        </div>
    </div>
    (only showing users not yet organizing the event)
    <div class="separator main-separator">
        <hr>
    </div>

    @include('pages.events.search-users', ['action' => 'Promote to Organizer', 'users' => $users, 'searchQuery' => $searchQuery])

</div>
@endsection