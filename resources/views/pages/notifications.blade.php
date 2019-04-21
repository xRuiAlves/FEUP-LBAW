@extends('layouts.app')

@section('css_includes')
<link href="{{ asset('css/notifications.css') }}" rel="stylesheet">
@endsection

@section('title', 'Notifications')

@section('content')
    <div id="background_wave"></div>

    <div id="page-card" class="container card-container font-content notifications-container">
        <div class="container notifications-list">
            <header>
                <h1>Notifications</h1>
            </header>
            @if(count($notifications) > 0)
                @foreach ($notifications as $notification)
                    <div class="row no-gutters notification-item">
                        <div class="col-12">
                            <div class="row header">
                            <a class="col-8 col-md-10 title font-title" href="{{$notification->href}}">
                                    {{$notification->title}}
                                    <div class="timestamp">
                                        {{$notification->formatted_timestamp}}
                                    </div>
                                </a>
                                <div class="col-4 col-md-2 ml-auto actions d-flex justify-content-end">
                                    <div class="row">
                                        <span class="col-6 delete">
                                            <i class="fas fa-times"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 description">
                                    {{$notification->message}}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                {{$notifications->links("pagination::bootstrap-4")}}
            @else
                <h2>You have no notifications</h2>
            @endif
        </div>
    </div>
@endsection