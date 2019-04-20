@extends('layouts.app')

@section('css_includes')
<link href="{{ asset('css/main_page.css') }}" rel="stylesheet">
@endsection

@section('title', 'Eventually Homepage')

@section('body')

<script src="{{ asset('js/main_page.js') }}" defer></script>

<div id="banner-wrapper">
    <div id="banner-image-container">
        <img src="{{asset('images/concert2.jpg')}}" />
        <img src="{{asset('images/concert1.jpg')}}" />
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
    @if(Auth::guest())
    <div class="banner-corner-actions">
        <a href="{{route('login')}}" title="Log In">
            Login
        </a>
        <a href="{{route('register')}}" title="Register">
            Register
        </a>
    </div>
    @endif
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
                        <button class="host-event" onclick="window.location.href='/create_event.html'">Host an
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
    <div class="row">
        <div class="col-12 col-lg-4">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search" class="search-field" />
        </div>
        <div class="col-12 col-sm-3 col-lg-2">
            <i class="fas fa-map-marker-alt"></i>
            <input type="text" placeholder="Location" />
        </div>
        <div class="col-12 col-sm-3 col-lg-2">
            <div class="dropdown">
                <button class="btn dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                    Category
                </button>
                <div class="dropdown-menu scrollable-menu">
                    <a class="dropdown-item" href="#">Sports</a>
                    <a class="dropdown-item" href="#">Arts</a>
                    <a class="dropdown-item" href="#">Technology</a>
                    <a class="dropdown-item" href="#">Animals</a>
                    <a class="dropdown-item" href="#">Learning</a>
                    <a class="dropdown-item" href="#">Politics</a>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-3 col-lg-2">
            <button class="btn date">Start
                <span>
                    <i class="far fa-calendar-alt"></i>
                </span>
            </button>
        </div>
        <div class="col-12 col-sm-3 col-lg-2">
            <button class="btn date">End
                <span>
                    <i class="far fa-calendar-alt"></i>
                </span>
            </button>
        </div>
    </div>
</div>

<div class="events">
    @foreach ($events as $event)
    <a class="container card-container event-card" href="/event/{{$event->id}}">
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
                {{$event->category->name}}
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