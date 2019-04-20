@extends('layouts.admin', ['activeTable' => 'events'])

@section('table')
<div id="events-table" class="admin-dashboard col-12 col-md-11">
    <div class="collapse-title custom-title">Events</div>
        <div class="searchbar-container">
            <input class="searchbar" type="text" placeholder="Event name, location, ..." />
            <i class="fas fa-search icon-right"></i>
        </div>
        <div class="row no-gutters">
            <div class="col-12">
                <button class="btn action-btn">Disable selected events</button>
                <button class="btn action-btn">Enable selected events</button>
            </div>
        </div>
        <div class="content-table">
            <table class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Title</th>
                        <th>Status</th>
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
                        <td>{{$event->title}}</td>
                        <td>{{$event->status}}</td>
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