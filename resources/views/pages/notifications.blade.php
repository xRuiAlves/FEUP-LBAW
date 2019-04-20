@extends('layouts.app')

@section('css_includes')
<link href="{{ asset('css/notifications.css') }}" rel="stylesheet">
@endsection

@section('title', 'Notifications')

@section('content')
    <div class="regular-wave" id="background_wave"></div>

    <div id="page-card" class="container card-container font-content notifications-container">
        <header>
            <h1>Notifications</h1>
        </header>
        @if(count($notifications) > 0)
            @foreach ($notifications as $notification)
                <div>
                    <h4>{{$notification}}</h4>
                </div>
            @endforeach
            {{$notifications->links("pagination::bootstrap-4")}}
        @else
            <h2>You have no notifications</h2>
        @endif
    </div>
@endsection