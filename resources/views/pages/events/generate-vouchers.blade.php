@extends('layouts.app')

@section('asset_includes')
<link href="{{ asset('css/event_page.css') }}" rel="stylesheet">
<link href="{{ asset('css/event_management.css') }}" rel="stylesheet">
@endsection

@section('scripts')
<script src="{{ asset('js/generate_vouchers.js')}}" defer></script>
@endsection

@section('title', 'Generate Voucher - ' . $event->title . ' - Eventually')

@section('content')
<div id="background_wave"></div>

<div id="page-card" class="container card-container font-content event-card" data-event_id="{{$event->id}}">
    <div class="row no-gutters main">
        <div class="col-12 event-title font-title">
                Generate Voucher - {{$event->title}}
        </div>
    </div>
    <div class="separator main-separator">
        <hr>
    </div>

    <label id="vouchers-input-label">Please insert the number of vouchers you wish to generate:</label><br>
    <input id="number-vouchers" min="1" max="10" value="1" type="number" placeholder="Number of Vouchers">
    <br>
    <button id="btn-generator" type="button" class="btn btn-secondary">Generate 1 voucher</button>
    <div id="vouchers-output"></div>
    <br><br><br>


</div>
@endsection