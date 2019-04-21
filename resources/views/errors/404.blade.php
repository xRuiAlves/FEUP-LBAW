@extends('layouts.app')

@section('css_includes')
<link href="{{ asset('css/error_pages.css') }}" rel="stylesheet">
@endsection

@section('title', '404 - Not Found')

@section('content')
    <div class="regular-wave" id="background_wave"></div>
    
    <div id="page-card" class="container card-container font-content not-found-container">
        <header>
            <h1>404 - Not found</h1>
        </header>
        <div class="mobile-wave" id="background_wave"></div>
        <div class="not-found-body">
            <p>
                @if(strlen($exception->getMessage()) === 0)
                    The resource that you requested does not seem to exist.
                @else
                    {{ $exception->getMessage() }}
                @endif
            </p>
            <p>
                To go back to the application click
                <a href="/">here</a>.
            </p>
        </div>
    </div>
@endsection