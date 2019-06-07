@extends('layouts.app')

@section('asset_includes')
<link href="{{ asset('css/event_page.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/create_event.css') }}">
@endsection

@section('scripts')
<script src="{{ asset('js/setlocale-datetime.js') }}" type="text/javascript" defer></script>
<script src="{{ asset('js/create_event.js') }}" type="text/javascript" defer></script>
@endsection

@section('title', (empty($event) ? "Create Event" : ("Edit " . $event->title)) . ' - Eventually')

@section('content')

<div id="background_wave"></div>

<div id="page-card" class="container card-container font-content event-card event-creation-container">
    <form novalidate class="needs-validation" action="{{empty($event) ? "/event/create" : "/event/".$event->id."/edit"}}" method="post">
        <fieldset>
        <legend style="display:none;">Create event form</legend>
        {{ csrf_field() }}
        <header class="row no-gutters">
            <div class="col-12">
                <div class="form-group">
                    <input class="form-control title-input" autocomplete="off" value="{{empty($event) ? Request::old('title') : (empty(Request::old('title')) ? $event['title'] : Request::old('title'))}}" required type="text" name="title" placeholder="Title" aria-label="Title">
                    <i class="fas fa-question-circle form-info" data-toggle="popover" data-placement="top" data-content="In this page, you may create a new Event. All fields are mandatory, except for the ones marked as optional. You will be able to add other users to the organization team after the event is created."></i>
                    <div class="invalid-feedback">
                        Please provide a title for the event
                    </div>
                </div>
            </div>
        </header>
        <div class="mobile-wave" id="background_wave"></div> <!-- why tho -->
        <div class="row no-gutters event-details font-title">
            <div class="col-12 col-lg-6 event-spacetime">
                <div class="row no-gutters date event-field form-group">
                    <div class="col-12">
                        <label> 
                            <span>
                                <i class="far fa-calendar-alt icon-left"></i>
                            </span>
                            Start
                            <div class="input-group date" id="datetimepicker_start" data-target-input="nearest">
                                <input type="text" class="form-control datetimepicker-input" required data-target="#datetimepicker_start" value="{{empty($event) ? Request::old('start_timestamp') : (empty(Request::old('start_timestamp')) ? $event['start_timestamp'] : Request::old('start_timestamp'))}}" name="start_timestamp" aria-label="Start Date"/>
                                <div class="input-group-append" data-target="#datetimepicker_start" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                            <div class="invalid-feedback">Please provide at least a start date for the event</div>
                            

                        </label>
                    </div>
                    <div class="col-12">
                        <label> End
                            <div class="input-group date" id="datetimepicker_end" data-target-input="nearest">
                                <input type="text" class="form-control datetimepicker-input" data-target="#datetimepicker_end" value="{{empty($event) ? Request::old('end_timestamp') : (empty(Request::old('end_timestamp')) ? $event['end_timestamp'] : Request::old('end_timestamp'))}}" name="end_timestamp" aria-label="End Date" placeholder="(Optional)"/>
                                <div class="input-group-append" data-target="#datetimepicker_end" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                            <div class="invalid-feedback">Please provide at least a start date for the event</div>
                        </label>
                    </div>
                    <div class="col-11 separator main-separator">
                        <br><hr>
                    </div>
                    <div class="row no-gutters">
                        <div class="col-12 text-left">
                            <h4>Ticket limit</h4>
                        </div>
                        <div class="col-12 text-left price event-field">
                            <div class="form-group">
                                <input class="form-control" autocomplete="off" type="text" name="capacity" value="{{empty($event) ? Request::old('capacity') : (empty(Request::old('capacity')) ? $event['capacity'] : Request::old('capacity'))}}" min="1" placeholder="No maximum set (optional)" aria-label="Capacity">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6 event-category">
                <div class="row no-gutters">
                    <div class="col-12 text-left">
                        <h4>Price</h4>
                    </div>
                    <div class="col-12 text-left price event-field">
                        <div class="form-group">
                            <input class="form-control" autocomplete="off" required type="text" name="price" value="{{empty($event) ? Request::old('price') : (empty(Request::old('price')) ? $event['price'] : Request::old('price'))}}" min="0" placeholder="0.00" aria-label="Price">
                            <div class="invalid-feedback">Please provide a valid price for the event</div>
                            €
                        </div>
                    </div>
                </div>
                <div class="separator main-separator">
                    <hr>
                </div>
                <div class="row no-gutters">
                    <div class="col-12 text-left">
                        <h4>Category</h4>
                    </div>
                    <div class="col-12 event-field">
                        <div class="dropdown category-picker form-group">
                            <select required name="event_category_id" class="custom-select">
                                <option value="" selected disabled>Pick a category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        @if(empty($event))
                                            @if(Request::old("event_category_id") == $category->id)
                                                selected
                                            @endif
                                        @else 
                                            @if(empty(Request::old('event_category_id')))
                                                @if($event['event_category_id'] == $category->id)
                                                    selected
                                                @endif
                                            @else
                                                @if(Request::old("event_category_id") == $category->id)
                                                    selected
                                                @endif
                                            @endif
                                        @endif
                                    >{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Please select a category for the event</div>
                        </div>
                    </div>
                </div>
                <div class="separator main-separator">
                    <hr>
                </div>
                <div class="row no-gutters">
                    <div class="col-12 text-left">
                        <h4>Tags</h4>
                        Write a comma or click the '+' button to add a tag
                    </div>
                    <div id="added-tags">
                        @if(!empty(Request::old("tags")))
                            @foreach(json_decode(Request::old("tags")) as $tag)
                                <button class="added-tag btn btn-light" value="{{$tag}}">{{$tag}} &times;</button>
                            @endforeach
                        @elseif(!empty($event))
                            @foreach($event->tags as $tag)
                                <button class="added-tag btn btn-light" value="{{$tag->name}}">{{$tag->name}} &times;</button>
                            @endforeach
                        @endif
                    </div>
                    <div class="col-12 text-left">
                        <input id="add-tag-input" type="text" placeholder="Add a tag">
                        <i id="add-tag-button" class="fas fa-plus"></i>
                        <input id="added-tags-string" name="tags" type="text" value='{{
                            empty($event) ? 
                            Request::old('tags') 
                            : 
                            (empty(Request::old('tags')) ? 
                                $event->tags->map(function ($tag) {
                                    return $tag->name;
                                })
                                : Request::old('tags')
                            )}}'>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="separator main-separator">
                    <br><hr>
                </div>
                <div class="row no-gutters">
                    <div class="col-12 location event-field">
                        <div class="form-group">
                            <span>
                                <i class="fas fa-map-marker-alt icon-left"></i>
                            </span>
                            <input class="form-control" type="text" name="location" value="{{empty($event) ? Request::old('location') : (empty(Request::old('location')) ? $event['location'] : Request::old('location'))}}" placeholder="Location (optional)" aria-label="Location">
                            <div class="invalid-feedback">Please provide a valid location for the event</div>
                        </div>
                    </div>
                </div>
                <div id="map_wrapper">
                    <iframe class="event-map"
                        src="https://maps.google.com/?q={{empty($event) ? Request::old('latitude') : (empty(Request::old('latitude')) ? $event['latitude'] : Request::old('latitude'))}},{{empty($event) ? Request::old('longitude') : (empty(Request::old('longitude')) ? $event['longitude'] : Request::old('longitude'))}}&ie=UTF8&t=&z=14&iwloc=B&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0">
                    </iframe>
                </div>
                <input type="hidden" name="latitude" value="{{empty($event) ? Request::old('latitude') : (empty(Request::old('latitude')) ? $event['latitude'] : Request::old('latitude'))}}">
                <input type="hidden" name="longitude" value="{{empty($event) ? Request::old('longitude') : (empty(Request::old('longitude')) ? $event['longitude'] : Request::old('longitude'))}}">
                <small>Latitude and Longitude searching provided by <a href="http://nominatim.org/">Nominatim</a></small>
            </div>
        </div>
        <div class="row no-gutters event-description">
            <div class="col-12">
                <div class="form-group">
                    <textarea class="form-control" required name="description" placeholder="Event description">{{empty($event) ? Request::old('description') : (empty(Request::old('description')) ? $event['description'] : Request::old('description'))}}</textarea>
                    <div class="invalid-feedback">Please provide a description for the event</div>
                </div>
            </div>
        </div>
        <footer class="row no-gutters">
            <div class="col-12 create-btn">
                <button type="submit" class="btn">
                    {{empty($event) ? "Create" : "Confirm Changes"}}
                    <span>
                        <i class="fas fa-check icon-right"></i>
                    </span>
                </button>
            </div>
        </footer>
        @if ($errors->any())
            <div class="form-errors">
            @foreach ($errors->all() as $error)
                <p class="submission-error">
                    {{ $error }}
                </p>       
            @endforeach
            </div>
        @endif
        </fieldset>
    </form>
</div>
@endsection