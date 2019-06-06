@extends('layouts.app')

@section('asset_includes')
<link href="{{ asset('css/event_page.css') }}" rel="stylesheet">
<link href="{{ asset('css/event_management.css') }}" rel="stylesheet">
@endsection

@section('scripts')
<script src="{{ asset('js/invite.js')}}" defer></script>
@endsection

@section('title', 'Invite - ' . $event->title . ' - Eventually')

@section('content')
<div id="background_wave"></div>

<div id="page-card" class="container card-container font-content event-card" data-event_id="{{$event->id}}">
    <div class="row no-gutters main">
        <div class="col-12 event-title font-title">
                Invite - {{$event->title}}
        </div>
    </div>
    (only showing users not yet invited or attending the event)
    <div class="separator main-separator">
        <hr>
    </div>
    <div id="event-invite-status-messages" class="status-messages">
        <div class="alert alert-danger" style="display:none;white-space:pre-line"></div>
        <div class="alert alert-success" style="display:none;white-space:pre-line"></div>
    </div>
    @include('pages.events.search-users', ['action' => 'Invite', 'users' => $users, 'searchQuery' => $searchQuery])
</div>
@endsection