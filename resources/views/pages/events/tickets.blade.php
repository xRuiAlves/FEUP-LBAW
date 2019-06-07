@extends('layouts.app')

@section('asset_includes')
<link href="{{ asset('css/event_page.css') }}" rel="stylesheet">
<link href="{{ asset('css/event_management.css') }}" rel="stylesheet">
@endsection

@section('scripts')
<script src="{{ asset('js/event_tickets.js')}}" defer></script>
@endsection

@section('title', 'My Tickets - Eventually')

@section('content')
<div id="background_wave"></div>

<div id="page-card" class="container card-container font-content event-card" data-event_id="{{$event->id}}">
    <div class="row no-gutters main">
        <header id="management-header">
            <h1>
                Tickets<span class="event-title-name"> - {{$event->title}}</span>
            </h1>
        </header>
    </div>
    <div id="event-tickets-status-messages" class="status-messages">
            <div class="alert alert-danger" style="display:none;white-space:pre-line"></div>
            <div class="alert alert-success" style="display:none;white-space:pre-line"></div>
        </div>
    <div class="separator main-separator">
        <hr>
    </div>

    <div class="content-table">
        <div class="row">
            <h4 class="col-12 col-md-8">Tickets</h4>
        </div>
        @if(count($tickets) === 0)
            <p>You have no tickets for this event yet.</p>
        @else
        <table class="table">
            <thead>
                <tr>
                    <th>Ticket ID</th>
                    <th>Billing Name</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($tickets as $ticket_full)
                {{$ticket_full}}
                @php
                    $ticket = $ticket_full['ticket'];
                @endphp
                <tr class="ticket" data-user_id="{{Auth::user()}}">
                    <td>
                        {{$ticket->id}}
                    </td>
                    <td>
                        {{$ticket->billing_name}}
                    </td>
                    <td class="text-right">
                        <button type="button" data-user-id="{{Auth::user()->id}}" data-ticket-id="{{$ticket->id}}" data-event-id="{{$event->id}}" class="btn cancel-ticket">Cancel <i class="fas fa-trash-alt"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{$tickets->links("pagination::bootstrap-4")}}
        @endif
        
    </div>


</div>
@endsection