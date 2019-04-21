@extends('layouts.admin', ['activeTable' => 'events'])

@section('css_includes')
@parent
<script src="{{asset('js/admin_user_editing.js')}}" defer></script>
@endsection

@section('table')
<div id="events-table" class="admin-dashboard col-12 col-md-10 col-xl-11">
    <div class="collapse-title custom-title">Events</div>
        <div class="searchbar-container">
            <input class="searchbar" type="text" placeholder="Event name, location, ..." />
            <i class="fas fa-search icon-right"></i>
        </div>
        <div class="row no-gutters">
            <div class="col-12">
                <button id="disable_events_btn" class="btn action-btn">Disable selected events</button>
                <button id="enable_events_btn" class="btn action-btn">Enable selected events</button>
            </div>
        </div>
        <div class="content-table">
            <table class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Title</th>
                        <th>is Disabled</th>
                        <th>is Cancelled</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Location</th>
                        <th>Pricing</th>
                    </tr>
                </thead>
                <tbody>  
                    @foreach($events as $event)              
                    <tr>
                        <td><input type="checkbox"/></td>
                        <td><a href="{{$event->href}}">{{$event->title}}</a></td>
                        <td>{{$event->is_disabled ? "Yes" : "No"}}</td>
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