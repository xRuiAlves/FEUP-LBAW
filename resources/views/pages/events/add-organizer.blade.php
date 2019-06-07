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
        <header id="management-header">
        <h1>
            Add Organizer<span class="event-title-name"> - {{$event->title}}</span>
        </h1>
    </header>
    <div id="shown-users-note">Only showing users not yet organizing the event</div>
    <div class="separator main-separator">
        <hr>
    </div>

    <div id="event-add-organizer-status-messages" class="status-messages">
        <div class="alert alert-danger" style="display:none;white-space:pre-line"></div>
        <div class="alert alert-success" style="display:none;white-space:pre-line"></div>
    </div>

    @include('pages.events.search-users', ['action' => 'Promote to Organizer', 'users' => $users, 'searchQuery' => $searchQuery])

</div>
@endsection