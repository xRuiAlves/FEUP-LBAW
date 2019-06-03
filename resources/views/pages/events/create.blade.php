@extends('layouts.app')

@section('asset_includes')
<link href="{{ asset('css/event_page.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/create_event.css') }}">
@endsection

@section('title', 'Create Event - Eventually')

@section('content')
<div id="background_wave"></div>

<div id="page-card" class="container card-container font-content event-card event-creation-container">
    <form novalidate class="needs-validation" action="/event/create" method="post">
        {{ csrf_field() }}
        <header class="row no-gutters">
            <div class="col-12">
                <div class="form-group">
                    <input class="form-control title-input" value="{{Request::old('title')}}" required type="text" name="title" placeholder="Title">
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
                                <input type="text" class="form-control datetimepicker-input" required data-target="#datetimepicker_start" value="{{Request::old('start_timestamp')}}" name="start_timestamp"/>
                                <div class="input-group-append" data-target="#datetimepicker_start" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                            <div class="invalid-feedback">Please provide at least a start date for the event</div>
                            

                        </label>
                    </div>
                    <div class="col-12">
                        <label> End (Optional)
                            <div class="input-group date" id="datetimepicker_end" data-target-input="nearest">
                                <input type="text" class="form-control datetimepicker-input" data-target="#datetimepicker_end" value="{{Request::old('end_timestamp')}}" name="end_timestamp"/>
                                <div class="input-group-append" data-target="#datetimepicker_end" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                            <div class="invalid-feedback">Please provide at least a start date for the event</div>
                        </label>
                    </div>
                </div>
                <div class="row no-gutters">
                    <div class="col-12 location event-field">
                        <div class="form-group">
                            <span>
                                <i class="fas fa-map-marker-alt icon-left"></i>
                            </span>
                            <input class="form-control" required type="text" name="location" value="{{Request::old('location')}}" placeholder="Location">
                            <div class="invalid-feedback">Please provide a valid location for the event</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6 event-category">
                <div class="row no-gutters">
                    <div class="col-12 price event-field">
                        <div class="form-group">
                            <input class="form-control" required type="text" name="price" value="{{Request::old('price')}}" min="0" placeholder="0.00">
                            <div class="invalid-feedback">Please provide a valid price for the event</div>
                        </div>
                        <span class="currency">€</span>
                    </div>
                </div>
                <div class="row no-gutters">
                    <div class="col-12 event-field">
                        <div class="dropdown category-picker form-group">
                            <select required name="event_category_id" class="custom-select">
                                <option value="" selected disabled>Pick a category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ (Request::old("event_category_id") == $category->id ? "selected":"") }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Please select a category for the event</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row no-gutters event-description">
            <div class="col-12">
                <div class="form-group">
                    <textarea class="form-control" required name="description" placeholder="Event description">{{Request::old('description')}}</textarea>
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
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <span class="submission-error">
                    {{ $error }}
                </span>       
            @endforeach
        @endif
    </form>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/setlocale-datetime.js') }}" type="text/javascript" defer>
</script>
@endsection