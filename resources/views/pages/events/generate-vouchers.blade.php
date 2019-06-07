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
    <header id="management-header">
        <h1>
            Generate Voucher<span class="event-title-name"> - {{$event->title}}</span>
        </h1>
    </header>
    <div class="separator main-separator">
        <hr>
    </div>

    <form id="voucher_gen_form" novalidate class="needs-validation">
    <fieldset>
        <legend style="display:none;">Generate Event Vouchers form</legend>
        <div class="form-group">
            <label id="vouchers-input-label">Please insert the number of vouchers you wish to generate:</label><br>
            <input id="number-vouchers" class="form-control" min="1" max="10" value="1" type="number" required autofocus placeholder="Number of Vouchers" aria-label="Number of Vouchers">
            <div class="invalid-feedback">
                Please insert a valid number of vouchers (positive integer).
            </div>
        </div>
        <button id="btn-generator" type="submit" class="btn btn-secondary">Generate 1 voucher</button>
    </fieldset>
    </form>
    <div id="vouchers-output"></div>
    <br><br><br>


</div>
@endsection