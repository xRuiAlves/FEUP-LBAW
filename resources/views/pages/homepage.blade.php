@extends('layouts.app', ['hideNavbar' => true])

@section('asset_includes')
<link href="{{ asset('css/main_page.css') }}" rel="stylesheet">
@endsection

@section('title', 'Eventually')

@section('content')

<script src="{{ asset('js/main_page.js') }}" defer></script>

<div id="banner-wrapper">
    <div id="banner-image-container">
        <img src="{{asset('images/concert2.jpg')}}" alt="In-door concert"/>
        <img src="{{asset('images/concert1.jpg')}}" alt="Out-door concert"/>
    </div>
    <div id="page-title">
        <div class="row no-gutters text-right">
            <div class="col-12">
                <h1>Eventually</h1>
            </div>
        </div>
        <div class="row no-gutters text-right">
            <div class="col-12">
                <h2>Create. Attend. Organize.</h2>
            </div>
        </div>
    </div>
    <div class="banner-corner-actions">
    @if(Auth::guest())
        <a href="{{route('login')}}" title="Log In">
            Login
        </a>
        <a href="{{route('register')}}" title="Register">
            Register
        </a>
    @else
        <a href="{{route('logout')}}" title="Log Out">
            Logout
        </a>
    @endif
</div>
    <div id="banner-buttons">
        <div class="row">
            <div class="col-sm-12 col-md-8 col-lg-6 col-xl-4">
                <div class="container">
                    <div class="row">
                        <div class="col-12" id="banner-about-text">
                            <p>Create amazing experiences for everyone. Attend your favorite events. Organize easily
                                and
                                collaboratively.
                            </p>
                            <a href="#about">Find more about us!</a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <button class="find-events"
                            onclick="window.scrollTo(0, document.getElementById('search-box-anchor').offsetTop);">Find
                            Events</button>
                    </div>
                    <div class="col-12 col-md-6">
                        <button class="host-event" onclick="window.location.href='/event/create'">Host an
                            Event</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="banner-down-arrow">
        <div class="row">
            <div class="col-12">
                <a href="#search-box-anchor">
                    <i class="fas fa-angle-down"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div id="search-box-anchor"></div>

@include('inc.navbar')

<div class="container" id="search-box">
    <form action="/#search-box-anchor" method="get">
        <fieldset>
        <legend style="display:none;">Find events form</legend>
        <div class="row">
            <div class="col-12 col-lg-4">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search" aria-label="Search" name="search" class="search-field" />
            </div>
            <div class="col-12 col-sm-3 col-lg-2">
                <i class="fas fa-map-marker-alt"></i>
                <input type="text" name="location" placeholder="Location" aria-label="Location" class="location-field" />
            </div>
            <div class="col-12 col-sm-3 col-lg-2">
                <div class="dropdown">                
                    <select name="event_category" class="custom-select">
                        <option value="" selected disabled>Category</option>
                        @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-12 col-sm-3 col-lg-2">
                
                <button type="button" id="start_date_btn" class="btn date" data-toggle="collapse" data-target="#datetimepickerwrapper_start" aria-expanded="false" aria-controls="datetimepickerwrapper_start">
                    Start
                    <span>
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </button>
               
            </div>
            <div class="col-12 col-sm-3 col-lg-2">
                <button type="button" id="end_date_btn" class="btn date" data-toggle="collapse" data-target="#datetimepickerwrapper_end" aria-expanded="false" aria-controls="datetimepickerwrapper_end">
                    End
                    <span>
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </button>
            </div>
        </div>
        <div class="collapse" id="datetimepickerwrapper_start">
            <div class="date input-group" id="datetimepicker_start" data-target-input="nearest">
                <input id="start_input" type="text" class="form-control datetimepicker-input" data-target="#datetimepicker_start" value="" name="start_date" placeholder="Ex. 2019/05/20 10:37"  aria-label="Start Date"/>
                <div class="input-group-append" data-target="#datetimepicker_start" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                </div>
            </div>
        </div>
        <div class="collapse" id="datetimepickerwrapper_end">
            <div class="input-group date" id="datetimepicker_end" data-target-input="nearest">
                <input id="end_input" type="text" class="form-control datetimepicker-input" data-target="#datetimepicker_end" value="" name="end_date" placeholder="Ex. 2019/05/31 22:37" aria-label="End Date"/>
                <div class="input-group-append" data-target="#datetimepicker_end" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                </div>
            </div>
        </div>
        <input style="display: none" type=submit>
    </fieldset>
    </form>
</div>

<div class="events">
    @foreach ($events as $event)
    <a class="container card-container event-card" href="{{$event->href}}">
        <header class="row">
            <div class="col-12">
                <h3>{{$event->title}}</h3>
            </div>
            <div class="price-tag col-auto">
                @if($event->price == 0)
                    Free
                @else
                    {{$event->price}} €
                @endif
            </div>
            <div class="col-auto category">
                <span>
                    <i class="fas fa-tag"></i>
                </span>
                {{$event->category}}
            </div>
        </header>
        <footer class="row">
            <div class="col-12 location">
                <span>
                    <i class="fas fa-map-marker-alt"></i>
                </span>
                {{$event->location}}
            </div>
            <div class="col-7">
                <span>
                    <i class="far fa-calendar-alt"></i>
                </span>
                {{$event->start_date . ($event->end_date ? ' - ' . $event->end_date : '')}}
            </div>
            <div class="col-5 text-right">
                {{$event->start_time}}
                <span>
                    <i class="far fa-clock"></i>
                </span>
            </div>
        </footer>
    </a>
    @endforeach
    {{ $events->fragment('search-box-anchor')->links("pagination::bootstrap-4") }}

<div class="container-fluid white-section" id="about">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h4 class="footer-title">About Eventually</h4>
            </div>
        </div>
        <div class="row text-justify">
            <div class="col-12">
                <p>Eventually is the solution for finding and hosting events online, all over the world.</p>
                <p>This platform lets you easily find all kinds of events of your interest, allowing you to specify when,
                    where and what you want to participate in.
                    You can also manage your tickets and export your favorite events to Google™ Calendar for better organization.</p>
                <p>In our website, you can also create and manage any number of events you want, public or private, free
                    of charge - no matter what their scale is. Additionally, you may invite other users to help in their organization,
                    making the whole process easier.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/setlocale-datetime.js') }}" type="text/javascript" defer></script>
@endsection