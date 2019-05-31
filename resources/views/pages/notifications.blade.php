@extends('layouts.app')

@section('css_includes')
<link href="{{ asset('css/notifications.css') }}" rel="stylesheet">
<script src="{{asset('js/notifications.js')}}" defer></script>
@endsection

@section('title', 'Notifications - Eventually')

@section('content')
    <div id="background_wave"></div>

    <div id="page-card" class="container card-container font-content notifications-container">
        <div id="notifications-list" class="container notifications-list">
            <header>
                <h1>Notifications</h1>
                <i class="fas fa-question-circle form-info" data-toggle="popover" data-placement="top" data-content="In this page, you may view everything new that happened in Eventually, including changes in events and admin responses on issues you submitted."></i>
            </header>
            <div class="row no-gutters">
                <div id="status_messages" class="col-12">
                    <div class="alert alert-danger" style="display:none;white-space:pre-line"></div>
                    <div class="alert alert-success" style="display:none;white-space:pre-line"></div>
                </div>
            </div>
            @if(count($notifications) > 0)
                @foreach ($notifications as $notification)
                    <div class="row no-gutters notification-item" data-notification-id="{{$notification->id}}">
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
                                            <i class="fas fa-times dismiss-notification-button"></i>
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
                <h4>You have no available notifications.</h4>
            @endif
        </div>
    </div>

<div id="dismiss-notification-modal" class="modal fade font-content" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="header-container">
                    <div class="modal-title custom-modal-title">Dismiss notification</div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">                 
                Are you sure you want to dismiss this notification?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn publish-button dismiss-notification">Dismiss</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection