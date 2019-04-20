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
        @foreach ($events as $event)
        <div>
            <h5>{{$event}}</h5>
            @if($event->user_id == $user->id)
                <small>Creator</small>
            @else
                <small>Not Creator</small>
            @endif
        </div>
        @endforeach

        <div class="timeline">

            <div class="sticky-container">
                <div class="item-year-month sticky-item">
                    <div class="item-month">Apr</div>
                    <div class="item-year">2019</div>
                </div>
                <div class="dashboard-day-containers">
                    <div class="dashboard-day-container sticky-container">
                        <div class="item-day sticky-item">
                            <div class="item-day-number">18</div>
                            <div class="item-day-week">TUE</div>
                        </div>
                        <div class="dashboard-day-items">
                            <a class="dashboard-day-item item-type-organizer" href="/event_page_admin.html">
                                <header class="row">
                                    <div class="col-12">
                                        Functional Progamming using Phoenix + Elixir - The perks of being a Back End
                                        Developer
                                    </div>
                                </header>
                                <footer class="row">
                                    <div class="col-12 location">
                                        <span>
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        Madrid, Spain
                                    </div>
                                    <div class="col-6 time">
                                        <span>
                                            <i class="far fa-clock"></i>
                                        </span>
                                        15:50
                                    </div>
                                    <div class="col-6 text-right timespan"></div>
                                </footer>
                            </a>
                            <a class="dashboard-day-item item-type-attendee" href="/event_page_attendee.html">
                                <header class="row">
                                    <div class="col-12">
                                        Semana de Informática
                                    </div>
                                </header>
                                <footer class="row">
                                    <div class="col-12 location">
                                        <span>
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        Porto, Portugal
                                    </div>
                                    <div class="col-6 time">
                                        <span>
                                            <i class="far fa-clock"></i>
                                        </span>
                                        09:15
                                    </div>
                                    <div class="col-6 text-right timespan">[Day 1/2]</div>
                                </footer>
                            </a>
                            <a class="dashboard-day-item item-type-attendee" href="/event_page_disabled.html">
                                <header class="row">
                                    <div class="col-12">
                                        Fishing Workshop - HDNV Student Association
                                        <span class="not-enabled-event">[Disabled]</span>
                                    </div>
                                </header>
                                <footer class="row">
                                    <div class="col-12 location">
                                        <span>
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        Paris, France
                                    </div>
                                    <div class="col-6 time">
                                        <span>
                                            <i class="far fa-clock"></i>
                                        </span>
                                        18:30
                                    </div>
                                    <div class="col-6 text-right timespan"></div>
                                </footer>
                            </a>
                        </div>
                    </div>
                    <div class="dashboard-day-container sticky-container">
                        <div class="item-day sticky-item">
                            <div class="item-day-number">19</div>
                            <div class="item-day-week">WED</div>
                        </div>
                        <div class="dashboard-day-items">
                            <a class="dashboard-day-item item-type-organizer" href="/event_page_admin.html">
                                <header class="row">
                                    <div class="col-12">
                                        Politécnico Career Fest
                                    </div>
                                </header>
                                <footer class="row">
                                    <div class="col-12 location">
                                        <span>
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        Lisboa, Portugal
                                    </div>
                                    <div class="col-6 time">
                                        <span>
                                            <i class="far fa-clock"></i>
                                        </span>
                                        08:30
                                    </div>
                                    <div class="col-6 text-right timespan"></div>
                                </footer>
                            </a>
                            <a class="dashboard-day-item item-type-attendee" href="/event_page_cancelled.html">
                                <header class="row">
                                    <div class="col-12">
                                        Parkour @Miami Beach
                                        <span class="not-enabled-event">[Canceled]</span>
                                    </div>
                                </header>
                                <footer class="row">
                                    <div class="col-12 location">
                                        <span>
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        Miami, United States
                                    </div>
                                    <div class="col-6 time">
                                        <span>
                                            <i class="far fa-clock"></i>
                                        </span>
                                        10:00
                                    </div>
                                    <div class="col-6 text-right timespan"></div>
                                </footer>
                            </a>
                            <a class="dashboard-day-item item-type-attendee" href="/event_page_attendee.html">
                                <header class="row">
                                    <div class="col-12">
                                        Semana de Informática
                                    </div>
                                </header>
                                <footer class="row">
                                    <div class="col-12 location">
                                        <span>
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        Porto, Portugal
                                    </div>
                                    <div class="col-6 time">
                                        <span>
                                            <i class="far fa-clock"></i>
                                        </span>
                                        16:35
                                    </div>
                                    <div class="col-6 text-right timespan">[Day 2/2]</div>
                                </footer>
                            </a>
                            <a class="dashboard-day-item item-type-attendee" href="/event_page_attendee.html">
                                <header class="row">
                                    <div class="col-12">
                                        London Security Meetup - The secret life of SQL Injections
                                    </div>
                                </header>
                                <footer class="row">
                                    <div class="col-12 location">
                                        <span>
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        London, England
                                    </div>
                                    <div class="col-6 time">
                                        <span>
                                            <i class="far fa-clock"></i>
                                        </span>
                                        14:15
                                    </div>
                                    <div class="col-6 text-right timespan"></div>
                                </footer>
                            </a>
                        </div>
                    </div>
                    <div class="dashboard-day-container sticky-container">
                        <div class="item-day sticky-item">
                            <div class="item-day-number">21</div>
                            <div class="item-day-week">FRI</div>
                        </div>
                        <div class="dashboard-day-items">
                            <a class="dashboard-day-item item-type-organizer" href="/event_page_admin.html">
                                <header class="row">
                                    <div class="col-12">
                                        Functional Progamming using Phoenix + Elixir - The perks of being a Back End
                                        Developer
                                    </div>
                                </header>
                                <footer class="row">
                                    <div class="col-12 location">
                                        <span>
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        Madrid, Spain
                                    </div>
                                    <div class="col-6 time">
                                        <span>
                                            <i class="far fa-clock"></i>
                                        </span>
                                        15:50
                                    </div>
                                    <div class="col-6 text-right timespan"></div>
                                </footer>
                            </a>
                            <a class="dashboard-day-item item-type-attendee" href="/event_page_attendee.html">
                                <header class="row">
                                    <div class="col-12">
                                        Semana de Informática
                                    </div>
                                </header>
                                <footer class="row">
                                    <div class="col-12 location">
                                        <span>
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        Porto, Portugal
                                    </div>
                                    <div class="col-6 time">
                                        <span>
                                            <i class="far fa-clock"></i>
                                        </span>
                                        09:15
                                    </div>
                                    <div class="col-6 text-right timespan">[Day 1/2]</div>
                                </footer>
                            </a>
                        </div>
                    </div>
                    <div class="dashboard-day-container sticky-container">
                        <div class="item-day sticky-item">
                            <div class="item-day-number">22</div>
                            <div class="item-day-week">SAT</div>
                        </div>
                        <div class="dashboard-day-items">
                            <a class="dashboard-day-item item-type-organizer" href="/event_page_admin.html">
                                <header class="row">
                                    <div class="col-12">
                                        Politécnico Career Fest
                                    </div>
                                </header>
                                <footer class="row">
                                    <div class="col-12 location">
                                        <span>
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        Lisboa, Portugal
                                    </div>
                                    <div class="col-6 time">
                                        <span>
                                            <i class="far fa-clock"></i>
                                        </span>
                                        08:30
                                    </div>
                                    <div class="col-6 text-right timespan"></div>
                                </footer>
                            </a>
                            <a class="dashboard-day-item item-type-attendee" href="/event_page_attendee.html">
                                <header class="row">
                                    <div class="col-12">
                                        Semana de Informática
                                    </div>
                                </header>
                                <footer class="row">
                                    <div class="col-12 location">
                                        <span>
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        Porto, Portugal
                                    </div>
                                    <div class="col-6 time">
                                        <span>
                                            <i class="far fa-clock"></i>
                                        </span>
                                        16:35
                                    </div>
                                    <div class="col-6 text-right timespan">[Day 2/2]</div>
                                </footer>
                            </a>
                            <a class="dashboard-day-item item-type-attendee" href="/event_page_attendee.html">
                                <header class="row">
                                    <div class="col-12">
                                        London Security Meetup - The secret life of SQL Injections
                                    </div>
                                </header>
                                <footer class="row">
                                    <div class="col-12 location">
                                        <span>
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        London, England
                                    </div>
                                    <div class="col-6 time">
                                        <span>
                                            <i class="far fa-clock"></i>
                                        </span>
                                        14:15
                                    </div>
                                    <div class="col-6 text-right timespan"></div>
                                </footer>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sticky-container">
                <div class="item-year-month sticky-item">
                    <div class="item-month">May</div>
                    <div class="item-year">2019</div>
                </div>
                <div class="dashboard-day-containers">
                    <div class="dashboard-day-container sticky-container">
                        <div class="item-day sticky-item">
                            <div class="item-day-number">14</div>
                            <div class="item-day-week">TUE</div>
                        </div>
                        <div class="dashboard-day-items">
                            <a class="dashboard-day-item item-type-organizer" href="/event_page_admin.html">
                                <header class="row">
                                    <div class="col-12">
                                        Functional Progamming using Phoenix + Elixir - The perks of being a Back End
                                        Developer
                                    </div>
                                </header>
                                <footer class="row">
                                    <div class="col-12 location">
                                        <span>
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        Madrid, Spain
                                    </div>
                                    <div class="col-6 time">
                                        <span>
                                            <i class="far fa-clock"></i>
                                        </span>
                                        15:50
                                    </div>
                                    <div class="col-6 text-right timespan"></div>
                                </footer>
                            </a>
                            <a class="dashboard-day-item item-type-attendee" href="/event_page_attendee.html">
                                <header class="row">
                                    <div class="col-12">
                                        Semana de Informática
                                    </div>
                                </header>
                                <footer class="row">
                                    <div class="col-12 location">
                                        <span>
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        Porto, Portugal
                                    </div>
                                    <div class="col-6 time">
                                        <span>
                                            <i class="far fa-clock"></i>
                                        </span>
                                        09:15
                                    </div>
                                    <div class="col-6 text-right timespan">[Day 1/2]</div>
                                </footer>
                            </a>
                        </div>
                    </div>
                    <div class="dashboard-day-container sticky-container">
                        <div class="item-day sticky-item">
                            <div class="item-day-number">15</div>
                            <div class="item-day-week">MON</div>
                        </div>
                        <div class="dashboard-day-items">
                            <a class="dashboard-day-item item-type-organizer" href="/event_page_admin.html">
                                <header class="row">
                                    <div class="col-12">
                                        Politécnico Career Fest
                                    </div>
                                </header>
                                <footer class="row">
                                    <div class="col-12 location">
                                        <span>
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        Lisboa, Portugal
                                    </div>
                                    <div class="col-6 time">
                                        <span>
                                            <i class="far fa-clock"></i>
                                        </span>
                                        08:30
                                    </div>
                                    <div class="col-6 text-right timespan"></div>
                                </footer>
                            </a>
                            <a class="dashboard-day-item item-type-attendee" href="/event_page_attendee.html">
                                <header class="row">
                                    <div class="col-12">
                                        Semana de Informática
                                    </div>
                                </header>
                                <footer class="row">
                                    <div class="col-12 location">
                                        <span>
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        Porto, Portugal
                                    </div>
                                    <div class="col-6 time">
                                        <span>
                                            <i class="far fa-clock"></i>
                                        </span>
                                        16:35
                                    </div>
                                    <div class="col-6 text-right timespan">[Day 2/2]</div>
                                </footer>
                            </a>
                            <a class="dashboard-day-item item-type-attendee" href="/event_page_attendee.html">
                                <header class="row">
                                    <div class="col-12">
                                        London Security Meetup - The secret life of SQL Injections
                                    </div>
                                </header>
                                <footer class="row">
                                    <div class="col-12 location">
                                        <span>
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        London, England
                                    </div>
                                    <div class="col-6 time">
                                        <span>
                                            <i class="far fa-clock"></i>
                                        </span>
                                        14:15
                                    </div>
                                    <div class="col-6 text-right timespan"></div>
                                </footer>
                            </a>
                        </div>
                    </div>
                    <div class="dashboard-day-container sticky-container">
                        <div class="item-day sticky-item">
                            <div class="item-day-number">22</div>
                            <div class="item-day-week">MON</div>
                        </div>
                        <div class="dashboard-day-items">
                            <a class="dashboard-day-item item-type-organizer" href="/event_page_admin.html">
                                <header class="row">
                                    <div class="col-12">
                                        Functional Progamming using Phoenix + Elixir - The perks of being a Back End
                                        Developer
                                    </div>
                                </header>
                                <footer class="row">
                                    <div class="col-12 location">
                                        <span>
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        Madrid, Spain
                                    </div>
                                    <div class="col-6 time">
                                        <span>
                                            <i class="far fa-clock"></i>
                                        </span>
                                        15:50
                                    </div>
                                    <div class="col-6 text-right timespan"></div>
                                </footer>
                            </a>
                            <a class="dashboard-day-item item-type-attendee" href="/event_page_attendee.html">
                                <header class="row">
                                    <div class="col-12">
                                        Semana de Informática
                                    </div>
                                </header>
                                <footer class="row">
                                    <div class="col-12 location">
                                        <span>
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        Porto, Portugal
                                    </div>
                                    <div class="col-6 time">
                                        <span>
                                            <i class="far fa-clock"></i>
                                        </span>
                                        09:15
                                    </div>
                                    <div class="col-6 text-right timespan">[Day 1/2]</div>
                                </footer>
                            </a>
                        </div>
                    </div>
                    <div class="dashboard-day-container sticky-container">
                        <div class="item-day sticky-item">
                            <div class="item-day-number">29</div>
                            <div class="item-day-week">MON</div>
                        </div>
                        <div class="dashboard-day-items">
                            <a class="dashboard-day-item item-type-organizer" href="/event_page_admin.html">
                                <header class="row">
                                    <div class="col-12">
                                        Politécnico Career Fest
                                    </div>
                                </header>
                                <footer class="row">
                                    <div class="col-12 location">
                                        <span>
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        Lisboa, Portugal
                                    </div>
                                    <div class="col-6 time">
                                        <span>
                                            <i class="far fa-clock"></i>
                                        </span>
                                        08:30
                                    </div>
                                    <div class="col-6 text-right timespan"></div>
                                </footer>
                            </a>
                            <a class="dashboard-day-item item-type-attendee" href="/event_page_attendee.html">
                                <header class="row">
                                    <div class="col-12">
                                        Semana de Informática
                                    </div>
                                </header>
                                <footer class="row">
                                    <div class="col-12 location">
                                        <span>
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        Porto, Portugal
                                    </div>
                                    <div class="col-6 time">
                                        <span>
                                            <i class="far fa-clock"></i>
                                        </span>
                                        16:35
                                    </div>
                                    <div class="col-6 text-right timespan">[Day 2/2]</div>
                                </footer>
                            </a>
                            <a class="dashboard-day-item item-type-attendee" href="/event_page_attendee.html">
                                <header class="row">
                                    <div class="col-12">
                                        London Security Meetup - The secret life of SQL Injections
                                    </div>
                                </header>
                                <footer class="row">
                                    <div class="col-12 location">
                                        <span>
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        London, England
                                    </div>
                                    <div class="col-6 time">
                                        <span>
                                            <i class="far fa-clock"></i>
                                        </span>
                                        14:15
                                    </div>
                                    <div class="col-6 text-right timespan"></div>
                                </footer>
                            </a>
                        </div>
                    </div>
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