@extends('layouts.app')

@section('asset_includes')
<link href="{{ asset('css/event_page.css') }}" rel="stylesheet">
<link href="{{ asset('css/event_management.css') }}" rel="stylesheet">
@endsection

@section('scripts')
<script src="{{ asset('js/event_management.js')}}" defer></script>
@endsection

@section('title', 'Manage ' . $event->title . ' - Eventually')

@section('content')
<div id="background_wave"></div>

<div id="page-card" class="container card-container font-content event-card" data-event_id="{{$event->id}}">
    <div class="row no-gutters main">
        <div class="col-12 event-title font-title">
                {{$event->title}} - Management
        </div>
    </div>
    <div class="separator main-separator">
        <hr>
    </div>
    <button id="generate-vouchers" type="button" class="btn btn-secondary">Generate Voucher Codes</button>
    
    <br><br><br>

    <div class="content-table">
        <div class="row">
            <h4 class="col-12 col-md-8">Attendees</h4>
            <div class="col-12 col-md-4 text-right">
                <a href="./invite">Invite User <i class="fas fa-plus"></i></a>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Account Name</th>
                    <th>Billing Name</th>
                    <th>Email</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendees as $user)
                <tr class="attendee" data-user_id="{{$user->id}}">
                    <td>{{$user->name}}</td>
                    <td>
                        @if (empty($user->ticket->billing_name))
                            -
                        @else
                            {{$user->ticket->billing_name}}
                        @endif
                    </td>
                    <td>{{$user->email}}</td>
                    <td class="text-right">
                        @if ($user->ticket->is_checked_in)
                            <button type="button" class="btn" disabled>Checked In  <i class="fas fa-check"></i></button>
                        @else
                    <button type="button" class="btn btn-success check-in" data-user_id="{{$user->id}}">Check-In</button>
                        @endif
                    </td>
                    <td class="text-right"><button type="button" class="btn remove-attendee" data-user_id="{{$user->id}}"><i class="fas fa-trash-alt text-right"></i></button></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{$attendees->appends(['organizers' => $organizers->currentPage()])->links("pagination::bootstrap-4")}}
        
    </div>

    <br>

    @if ($isEventAdmin)
        <div class="content-table">
            <div class="row">
                <h4 class="col-12 col-md-8">Organizers</h4>
                <div class="col-12 col-md-4 text-right">
                    <a href="./add-organizer">Add Organizer <i class="fas fa-plus"></i></a>
                </div>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($organizers as $user)
                    <tr class="organizer" data-user_id="{{$user->id}}">
                        <td>{{$user->name}}</td>
                        <td>{{$user->email}}</td>
                        <td class="text-right">
                            @if ($user->id === $event->user_id)
                                <span class="text-muted">Event Admin</span>
                            @else
                                <button type="button" class="btn remove-organizer" data-user_id="{{$user->id}}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{$organizers->appends(['attendees' => $attendees->currentPage()])->links("pagination::bootstrap-4")}}
        </div>

        <button id="cancel-event-btn" type="button" class="btn btn-danger">Cancel Event</button>
    @else
        <button id="quit-organization-btn" type="button" class="btn btn-danger">Quit Event Organization</button>
    @endif


</div>
@endsection