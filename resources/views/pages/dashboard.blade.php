@extends('layouts.app')

@section('css_includes')
<link href="{{ asset('css/user_dashboard.css') }}" rel="stylesheet">
@endsection

@section('title', 'User Dashboard')

@section('content')
<div id="background_wave"></div>

<div id="page-card" class="container card-container font-content user-dashboard-container">
    <header class="row no-gutters">
        <div class="col-12 col-sm-8 title font-title">
            <h1>Your Events</h1>
        </div>
        <div class="col-12 col-sm-4 labels">
            <div class="label">Organizing
                <div class="label-color label-organizing">&nbsp;</div>
            </div>
            <div class="label">Attending
                <div class="label-color label-attending">&nbsp;</div>
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
                            <a href="/event/{{$event->id}}" class="dashboard-day-item {{$event->relationship === 'attendee' ? 'item-type-attendee' : 'item-type-organizer'}}">
                                <header class="row">
                                    <div class="col-12">
                                        {{$event->title}}
                                        @if($event->status !== 'Active')
                                        <span class="not-enabled-event">[{{$event->status}}]</span>
                                        @endif
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
                                    <div class="col-6 text-right timespan">TODO: [Day X/Y]</div>
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
        <h2>Not attending or organizing any event yet!</h2>
        <h3>Create an event <a href="/event/create">here</a> or search for events <a href="/#search-box-anchor">here!</a> (TODO: links)</h3>
    @endif
</div>
@endsection