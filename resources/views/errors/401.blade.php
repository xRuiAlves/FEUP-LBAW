@extends('layouts.app')

@section('asset_includes')
<link href="{{ asset('css/error_pages.css') }}" rel="stylesheet">
@endsection

@section('title', 'Unauthorized - Eventually')

@section('content')
    <div id="background_wave"></div>
    
    <div id="page-card" class="container card-container font-content not-found-container">
        <header>
            <h1>401 - Unauthorized</h1>
        </header>
        <div class="mobile-wave" id="background_wave"></div>
        <div class="not-found-body">
            <p>
                @if(strlen($exception->getMessage()) === 0)
                    You do not possess the required permissions to acces the requested resource.
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