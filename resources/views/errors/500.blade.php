@extends('layouts.app')

@section('asset_includes')
<link href="{{ asset('css/error_pages.css') }}" rel="stylesheet">
@endsection

@section('title', 'Internal Error - Eventually')

@section('content')
    <div id="background_wave"></div>
    
    <div id="page-card" class="container card-container font-content not-found-container">
        <header>
            <h1>500 - Internal Error</h1>
        </header>
        <div class="mobile-wave" id="background_wave"></div>
        <div class="not-found-body">
            <p>
                An internal error has occurred. We are sorry for any inconvenience caused. If the error persists, please contact the administration team.
            </p>
            <p>
                To go back to the application click
                <a href="/">here</a>.
            </p>
        </div>
    </div>
@endsection