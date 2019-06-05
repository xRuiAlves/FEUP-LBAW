@extends('layouts.admin', ['activeTable' => 'events'])

@section('asset_includes')
@parent
<script src="{{asset('js/admin_events_page.js')}}" defer></script>
@endsection

@section('table')
<div id="events-table" class="admin-dashboard col-12 col-md-10 col-xl-11">
    <div class="collapse-title custom-title">Events</div>
    <div class="searchbar-container">
        <form class="form-inline" action="" method="get">
            <label class="sr-only" for="inlineFormInputName2">Name</label>
            <input type="text" class="form-control mb-2 mr-sm-2" id="inlineFormInputName2" placeholder="Ex: SINF 2020" name="search" aria-label="Search Events">
            
            <button type="submit" class="btn btn-primary mb-2">Search</button>
        </form>
    </div>
        <div class="row no-gutters">
            <div class="col-12">
                <button id="disable_events_btn" class="btn action-btn">Disable selected events</button>
                <button id="enable_events_btn" class="btn action-btn">Enable selected events</button>
            </div>
            <div class="col-12 status-messages">
                <div class="alert alert-danger" style="display:none;white-space:pre-line"></div>
                <div class="alert alert-success" style="display:none;white-space:pre-line"></div>
            </div>
        </div>
        <div class="content-table">
            <table class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Title</th>
                        <th>Disabled</th>
                        <th>Cancelled</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Location</th>
                        <th>Pricing</th>
                    </tr>
                </thead>
                <tbody>  
                    @foreach($events as $event)              
                    <tr data-event-id="{{$event->id}}">
                        <td><input type="checkbox"/></td>
                        <td><a href="{{$event->href}}">{{$event->title}}</a></td>
                        <td class="event-abling">{{$event->is_disabled ? "Yes" : "No"}}</td>
                        <td>{{$event->is_cancelled ? "Yes" : "No"}}</td>
                        <td>{{$event->start_date}}</td>
                        <td>{{$event->end_date ? $event->end_date : '-'}}</td>
                        <td>{{$event->location}}</td>
                        <td>{{$event->price}}€</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{$events->links("pagination::bootstrap-4")}}
        </div>
    </div>
</div>
@endsection