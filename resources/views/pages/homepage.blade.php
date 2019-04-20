@extends('layouts.app')

@section('css_includes')
<link href="{{ asset('css/main_page.css') }}" rel="stylesheet">
@endsection

@section('title', 'Eventually Homepage')

@section('body')
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
    <div class="banner-corner-actions">
        <a href="" title="Log In" data-toggle="modal" data-target="#login_modal">
            Login
        </a>
        <a href="" title="Register" data-toggle="modal" data-target="#register_modal">
            Register
        </a>
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
                            onclick="window.scrollTo(0, document.getElementById('navbar').offsetTop);">Find
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
                <a href="#navbar">
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
            <div class="price-tag col-auto">{{$event->price}}€</div>
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
    {{ $events->fragment('search-box')->links("pagination::bootstrap-4") }}

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

<div id="login_modal" class="modal fade font-content" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title custom-modal-title font-title">Login</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form novalidate class="needs-validation redirect-on-submit font-content"
                    data-redirect-to="/index_lin.html">
                    <div class="form-group">
                        <input class="form-control" required type="email" name="email" placeholder="email">
                        <div class="invalid-feedback">
                            Please provide a valid email address
                        </div>
                    </div>
                    <div class="form-group">
                        <input class="form-control" required type="password" name="password" placeholder="password">
                        <div class="invalid-feedback">
                            Please type your password
                        </div>
                    </div>
                    <div class="d-flex justify-content-center">
                        <button class="my-btn my-btn-primary" type="submit">Login</button>
                    </div>
                </form>
                <div class="d-flex justify-content-center">
                    <a class="my-btn my-btn-borderless-secondary" href="/index_lin.html">
                        <span class="nav-icon icon-left">
                            <i class="fab fa-google" aria-hidden="true"></i>
                        </span>
                        Sign in with Google
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <span>Don't have an account yet? Register <a href=""
                        onclick="$('#login_modal').modal('hide'); $('#register_modal').modal('show'); return false;">here!</a></span>
            </div>
        </div>
    </div>
</div>

<div id="register_modal" class="modal fade font-content" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title custom-modal-title">Register</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form novalidate class="needs-validation redirect-on-submit" data-redirect-to="/">
                    <div class="form-group">
                        <input class="form-control" required type="email" name="email" placeholder="email">
                        <div class="invalid-feedback">
                            Please provide a valid email address
                        </div>
                    </div>
                    <div class="form-group">
                        <input class="form-control" required type="password" name="password" placeholder="password">
                        <div class="invalid-feedback">
                            Please type your password
                        </div>
                    </div>
                    <div class="form-group">
                        <input class="form-control" required type="password" name="password_repeat"
                            placeholder="repeat password">
                        <div class="invalid-feedback">
                            Please type your password again
                        </div>
                    </div>
                    <div class="d-flex justify-content-center">
                        <button class="my-btn my-btn-primary" type="submit">Register</button>
                    </div>
                </form>
                <div class="d-flex justify-content-center">
                    <a class="my-btn my-btn-borderless-secondary" href="/index_lin.html">
                        <span class="icon-left">
                            <i class="fab fa-google" aria-hidden="true"></i>
                        </span>
                        Sign in with Google
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <span>Already have an account? Login <a href=""
                        onclick="$('#register_modal').modal('hide'); $('#login_modal').modal('show'); return false;">here!</a></span>
            </div>
        </div>
    </div>
</div>
@endsection