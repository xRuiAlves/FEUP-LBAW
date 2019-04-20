@extends('layouts.app')

@section('css_includes')
<link href="{{ asset('css/event_page.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/create_event.css') }}">
@endsection

@section('title', 'Create Event')

@section('content')
<div class="regular-wave" id="background_wave"></div>

<div id="page-card" class="container card-container font-content event-card event-creation-container">
    <form novalidate class="needs-validation" action="/event/create" method="post">
        {{ csrf_field() }}
        <header class="row no-gutters">
            <div class="col-12">
                <div class="form-group">
                    <input class="form-control title-input" required type="text" name="title" placeholder="Title"></input>
                    <div class="invalid-feedback">
                        Please provide a title for the event
                    </div>
                </div>
            </div>
        </header>
        <div class="mobile-wave" id="background_wave"></div> <!-- why tho -->
        <div class="row no-gutters event-details font-title">
            <div class="col-12 col-sm-6 event-spacetime">
                <div class="row no-gutters date event-field">
                    <span>
                        <i class="far fa-calendar-alt icon-left"></i>
                    </span>
                    <div class="date-picker">
                        Pick a date
                    </div>
                </div>
                <div class="row no-gutters">
                    <div class="col-12 location event-field">
                        <div class="form-group">
                            <span>
                                <i class="fas fa-map-marker-alt icon-left"></i>
                            </span>
                            <input class="form-control" required type="text" name="location" placeholder="Location"></input>
                            <div class="invalid-feedback">Please provide a valid location for the event</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 event-category">
                <div class="row no-gutters">
                    <div class="col-12 price event-field">
                        <div class="form-group">
                            <input class="form-control" required type="text" name="price" placeholder="0.00"></input>
                            <div class="invalid-feedback">Please provide a valid price for the event</div>
                        </div>
                        <span class="currency">€</span>
                    </div>
                </div>
                <div class="row no-gutters">
                    <div class="col-12 event-field">
                        <div class="dropdown category-picker">
                            <span>
                                <i class="fas fa-tag"></i>
                            </span>
                            <button class="btn dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                Pick a category
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
                </div>
            </div>
        </div>
        <div class="row no-gutters event-description">
            <div class="col-12">
                <div class="form-group">
                    <textarea class="form-control" required name="description" placeholder="Event description"></textarea>
                    <div class="invalid-feedback">Please provide a description for the event</div>
                </div>
            </div>
        </div>
        <footer class="row no-gutters">
            <div class="col-12 create-btn">
                <button type="submit" class="btn">
                    Create
                    <span>
                        <i class="fas fa-check icon-right"></i>
                    </span>
                </button>
            </div>
        </footer>
    </form>
</div>
@endsection