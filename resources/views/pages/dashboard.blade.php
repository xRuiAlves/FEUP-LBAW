@extends('layouts.app')

@section('asset_includes')
<link href="{{ asset('css/user_dashboard.css') }}" rel="stylesheet">
@endsection

@section('title', 'Dashboard - Eventually')

@section('content')
<div id="background_wave"></div>

<div id="page-card" class="container card-container font-content user-dashboard-container">
    <header class="row no-gutters">
        <div class="col-12 col-sm-8 title font-title">
            <div class="title-container">
                <h1>
                    Dashboard
                    <i class="fas fa-question-circle form-info" data-toggle="popover" data-placement="top" data-content="In this page, you may view information of all events that you are attending and organizing."></i>
                </h1>
                
            </div>
        </div>
        <div class="col-12 col-sm-4 labels">
            <div class="label">Organizing
                <div class="label-color label-organizing">&nbsp;</div>
            </div>
            <div class="label">Attending
                <div class="label-color label-attending">&nbsp;</div>
            </div>
            <div class="label">Favorite
                <div class="label-color label-favorite">&nbsp;</div>
            </div>
        </div>
    </header>
    @if(count($events) > 0)
    <div class="timeline">
        @foreach ($events as $event)
            @if(empty($latest_month) || $event->start_date_month !== $latest_month)
                @if(!empty($latest_month))
                {{-- Need to close the previous tags --}}
                    {{-- dashboard-day-containers --}}
                    </div>
                {{-- sticky-container --}}
                </div>
                @endif
            {{-- Replace the sticky with a new month --}}
            <?php $latest_month = $event->start_date_month; $latest_day = $event->start_date_day; ?>
            <div class="sticky-container">
                <div class="item-year-month sticky-item">
                    <div class="item-month">{{$event->start_date_month}}</div>
                    <div class="item-year">{{$event->start_date_year}}</div>
                </div>
                <div class="dashboard-day-containers">
            @endif
                    <div class="dashboard-day-container sticky-container">
                        <div class="item-day sticky-item">
                            <div class="item-day-number">{{$event->start_date_day}}</div>
                            <div class="item-day-week">{{strtoupper($event->start_date_day_of_week)}}</div>
                        </div>
                        <div class="dashboard-day-items">
                            <a href="{{$event->href}}" class="dashboard-day-item 
                                @if($event->relationship === 'attendee') item-type-attendee
                                @endif
                                @if($event->relationship === 'organizer') item-type-organizer
                                @endif
                                @if($event->is_favorite) item-type-favorite
                                @endif
                            ">
                                <header class="row">
                                    <div class="col-12">
                                        {{$event->title}}
                                        @if($event->is_disabled || $event->is_cancelled)
                                        <span class="not-enabled-event">[{{$event->is_disabled ? 'Disabled' : 'Cancelled'}}]</span>
                                        @endif
                                        <span title="Favorited" class="favorite-marker"><i class="fas fa-star"></i></span>
                                    </div>
                                </header>
                                <footer class="row">
                                    <div class="col-12 location">
                                        <span><i class="fas fa-map-marker-alt"></i></span>
                                        {{$event->location}}
                                    </div>
                                    <div class="col-6 time">
                                        <span><i class="far fa-clock"></i></span>
                                        {{$event->start_time}}
                                    </div>
                                    <div class="col-6 text-right timespan"></div>
                                </footer>
                            </a>
                        </div>
                    </div>
        @endforeach
        {{-- Need to close the tags from the loop outside --}}
            {{-- dashboard-day-containers --}}
            </div>
        {{-- sticky-container --}}
        </div>
    </div>
    @else
        <h2>Not attending or organizing any event yet.</h2>
        <h3><a href="/event/create">Create an event</a> or <a href="/#search-box-anchor">search for events</a> to attend!</h3>
    @endif
</div>
@endsection