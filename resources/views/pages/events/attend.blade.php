@extends('layouts.app')

@section('asset_includes')
<link href="{{ asset('css/event_page.css') }}" rel="stylesheet">
<link href="{{ asset('css/attend_event.css') }}" rel="stylesheet">
@endsection

@section('title', 'Buy Tickets - Eventually')

@section('content')
    <div id="background_wave"></div>

<div id="page-card" class="container card-container font-content event-card" data-event-id={{$event->id}}>
    <div class="event-brief">
        
        <div class="row no-gutters main">
            <div class="col-12 col-lg-9 event-title font-title">
                <h1>
                    {{$event->title}} - Get Tickets
                </h1>
            </div>
        </div>
        <div class="mobile-wave" id="background_wave"></div>
        <div class="separator main-separator">
            <hr>
        </div>

        <div class="row">
            @if($event->attendees()->get()->contains(Auth::user()))
                <div class="col-12">
                    <h4 class="mb-5">You already have {{$event->attendees()->where('user_id', Auth::user()->id)->count()}} tickets for this event</h4>
                </div>    
            @endif
            <div class="col-12">
                <header class="d-flex align-items-center mb-3">
                    <h2 class="mb-0">
                        Ticket Order
                    </h2>
                    <i class="fas fa-question-circle form-info" data-toggle="popover" data-placement="top" data-content="In this page, you may buy tickets for the event. Please specify the NIF, Address, Billing Name and Voucher code that was given to you upon purchase, for each ticket."></i>
                </header>
                <form novalidate id="ticket-form" class="needs-validation" data-event-id="{{$event->id}}" action="/event/{{$event->id}}/attend" method="post">
                    {{ csrf_field() }}
                    <div id="tickets-container">
                        <div class="ticket">
                            <header>
                                <h3>Ticket #1</h3>
                            </header>
                            <div class="form-group">
                                {{-- <div class="form-group">
                                    <label for="nif-1">NIF:</label>
                                    <input class="form-control" type="number" id="nif-1" required name="nif" >
                            </div> --}}
                                <label>
                                    NIF:
                                    <input class="form-control" type="number" required name="nif" >
                                    <div class="invalid-feedback">asdasd</div>
                                </label>
                            </div>
                            <div class="form-group">
                                <label>
                                    Address:
                                    <input class="form-control" type="text" required name="address" >
                                    <div class="invalid-feedback">Please provide an address</div>
                                </label>
                            </div>
                            <div class="form-group">
                                <label>
                                    Billing Name:
                                    <input class="form-control" type="text" required name="billing_name" >
                                    <div class="invalid-feedback">Please provide a billing name</div>
                                </label>
                            </div>
                            <div class="form-group">
                                <label>
                                    Voucher Code:
                                    <input class="form-control" type="text" required name="voucher_code" >
                                    <div class="invalid-feedback"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn" id="add-ticket" title="Add Ticket">
                            <span>
                                <i class="fas fa-plus"></i>
                            </span>
                        </button>
                    </div>
    
                    <button type="submit" class="btn attend-btn">
                        Buy
                        <span>
                            <i class="fas fa-check icon-right"></i>
                        </span>
                    </button>
    
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
    <script src="{{ asset('js/attend_event.js') }}" defer></script>
@endsection