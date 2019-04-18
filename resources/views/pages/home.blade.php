@extends('layouts.app')

@section('content')
    <div id="banner-wrapper">
        <div id="banner-image-container">
            <img src="images/concert2.jpg" />
            <img src="images/concert1.jpg" />
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

    <nav class="navbar navbar-expand-lg font-title" id="navbar">
        <div class="container">
            <a class="navbar-brand" href="#">Eventually</a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon">
                    <i class="fas fa-bars"></i>
                </span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav nav-highlighted mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/#search-box-anchor">Search</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/create_event.html">Host</a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a href="" title="Log In" class="nav-link" data-toggle="modal" data-target="#login_modal">
                            Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="" title="Register" class="nav-link" data-toggle="modal" data-target="#register_modal">
                            Register
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

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
        <a class="container card-container event-card" href="/event_page.html">
            <header class="row">
                <div class="col-12">
                    <h3>Semana de Informática</h3>
                </div>
                <div class="price-tag col-auto">Free</div>
                <div class="col-auto category">
                    <span>
                        <i class="fas fa-tag"></i>
                    </span>
                    Technology
                </div>
            </header>
            <footer class="row">
                <div class="col-12 location">
                    <span>
                        <i class="fas fa-map-marker-alt"></i>
                    </span>
                    Porto, Portugal
                </div>
                <div class="col-7">
                    <span>
                        <i class="far fa-calendar-alt"></i>
                    </span>
                    18.04.2019
                </div>
                <div class="col-5 text-right">
                    09:15
                    <span>
                        <i class="far fa-clock"></i>
                    </span>
                </div>
            </footer>
        </a>

        <a class="container card-container event-card" href="/event_page.html">
            <header class="row">
                <div class="col-12">
                    <h3>Functional Progamming using Phoenix + Elixir - The perks of being a Back End Developer</h3>
                </div>
                <div class="price-tag col-auto">5.00€</div>
                <div class="col-auto category">
                    <span>
                        <i class="fas fa-tag"></i>
                    </span>
                    Learning
                </div>
            </header>
            <footer class="row">
                <div class="col-12 location">
                    <span>
                        <i class="fas fa-map-marker-alt"></i>
                    </span>
                    Madrid, Spain
                </div>
                <div class="col-7">
                    <span>
                        <i class="far fa-calendar-alt"></i>
                    </span>
                    18.04.2019
                </div>
                <div class="col-5 text-right">
                    15:50
                    <span>
                        <i class="far fa-clock"></i>
                    </span>
                </div>
            </footer>
        </a>

        <a class="container card-container event-card" href="/event_page.html">
            <header class="row">
                <div class="col-12">
                    <h3>Mannheim fußball treffen</h3>
                </div>
                <div class="price-tag col-auto">Free</div>
                <div class="col-auto category">
                    <span>
                        <i class="fas fa-tag"></i>
                    </span>
                    Sports
                </div>
            </header>
            <footer class="row">
                <div class="col-12 location">
                    <span>
                        <i class="fas fa-map-marker-alt"></i>
                    </span>
                    Mannheim, Germany
                </div>
                <div class="col-7">
                    <span>
                        <i class="far fa-calendar-alt"></i>
                    </span>
                    19.04.2019
                </div>
                <div class="col-5 text-right">
                    08:00
                    <span>
                        <i class="far fa-clock"></i>
                    </span>
                </div>
            </footer>
        </a>

        <a class="container card-container event-card" href="/event_page.html">
            <header class="row">
                <div class="col-12">
                    <h3>London Security Meetup - The secret life of SQL Injections</h3>
                </div>
                <div class="price-tag col-auto">7.50€</div>
                <div class="col-auto category">
                    <span>
                        <i class="fas fa-tag"></i>
                    </span>
                    Learning
                </div>
            </header>
            <footer class="row">
                <div class="col-12 location">
                    <span>
                        <i class="fas fa-map-marker-alt"></i>
                    </span>
                    London, England
                </div>
                <div class="col-7">
                    <span>
                        <i class="far fa-calendar-alt"></i>
                    </span>
                    20.04.2019
                </div>
                <div class="col-5 text-right">
                    14:15
                    <span>
                        <i class="far fa-clock"></i>
                    </span>
                </div>
            </footer>
        </a>

        <a class="container card-container event-card" href="/event_page.html">
            <header class="row">
                <div class="col-12">
                    <h3>Politécnico Career Fest</h3>
                </div>
                <div class="price-tag col-auto">12.00€</div>
                <div class="col-auto category">
                    <span>
                        <i class="fas fa-tag"></i>
                    </span>
                    Technology
                </div>
            </header>
            <footer class="row">
                <div class="col-12 location">
                    <span>
                        <i class="fas fa-map-marker-alt"></i>
                    </span>
                    Lisboa, Portugal
                </div>
                <div class="col-7">
                    <span>
                        <i class="far fa-calendar-alt"></i>
                    </span>
                    20.04.2019
                </div>
                <div class="col-5 text-right">
                    08:30
                    <span>
                        <i class="far fa-clock"></i>
                    </span>
                </div>
            </footer>
        </a>
    </div>

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

    <footer class="container-fluid page-footer">
        <div class="container">
            <div class="row">
                <h4 class="col-12 footer-title">Contacts</h4>
                <div class="col-12">
                    <p class="contact-item">Rua Dr. Roberto Frias, 4200-465 Porto, Portugal</p>
                    <p class="contact-item">+351 22 508 1440</p>
                    <p class="contact-item contact-item-mail"><a href="mailto:help@eventual.ly">help@eventual.ly</a></p>
                    <p class="footer-questions">Have a question? Visit our <a class="footer-link" href="/faq.html">FAQs</a> or visit our <a class="footer-link" href="#about">about</a> section.</p>
                </div>
            </div>
            <br><br>
            <div class="row">
                <div class="col-12 text-center">
                    <p>© Copyright 2019. All rights eventually reserved</p>
                </div>
            </div>
        </div>
    </footer>