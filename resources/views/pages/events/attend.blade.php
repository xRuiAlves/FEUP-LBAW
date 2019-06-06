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

            <div class="col-12">
                <h2>
                    Ticket Order
                </h2>
                <form novalidate id="ticket-form" class="needs-validation" data-event-id="{{$event->id}}" method="post">
                    {{ csrf_field() }}
                    <div id="tickets-container">
                        <div class="ticket">
                            <header>
                                <h3>Ticket #1</h3>
                            </header>
                            <div class="form-group">
                                <label>
                                    NIF:
                                    <input class="form-control" type="number" required name="nif" >
                                    {{-- <div class="invalid-feedback">Please provide a NIF</div> --}}
                                </label>
                            </div>
                            <div class="form-group">
                                <label>
                                    Address:
                                    <input class="form-control" type="text" required name="address" >
                                    {{-- <div class="invalid-feedback">Please provide an address</div> --}}
                                </label>
                            </div>
                            <div class="form-group">
                                <label>
                                    Billing Name:
                                    <input class="form-control" type="text" required name="billing_name" >
                                    {{-- <div class="invalid-feedback">Please provide a billing name</div> --}}
                                </label>
                            </div>
                            <div class="form-group">
                                <label>
                                    Voucher Code:
                                    <input class="form-control" type="text" name="voucher_code" >
                                    {{-- <div class="invalid-feedback"></div> --}}
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
    <script type="text/javascript" src="{{ asset('js/attend_event.js') }}" defer></script>
@endsection