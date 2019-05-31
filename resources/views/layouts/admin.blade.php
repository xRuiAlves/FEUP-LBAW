@extends('layouts.app', ['hideFooter' => true])

@section('asset_includes')
<link href="{{ asset('css/admin_dashboard.css') }}" rel="stylesheet">
@endsection

@section('title', 'Admin Dashboard - Eventually')

@section('content')

<div id="admin-dashboard" class="font-content admin-dashboard-container" data-admin-id={{Auth::user()->id}}>
    <header class="font-title">
        <h1>Administration Dashboard</h1>
    </header>
    <div class="mobile-wave" id="background_wave"></div>
    <div class="separator">
        <hr>
    </div>
    <div class="row" id="admin-tabs">
        <div class="col-12 col-md-2 col-xl-1">
            <div class="row no-guters nav nav-pills" role="tablist">
                <a id="user-tab" class="col-3 col-md-12 container admin-tab {{$activeTable === 'users' ? 'active' : ''}}" href="/admin/users">
                    Users
                </a>
                <a id="events-tab" class="col-3 col-md-12 container admin-tab {{$activeTable === 'events' ? 'active' : ''}}" href="/admin/events">
                    Events
                </a>
                <a id="categories-tab" class="col-3 col-md-12 container admin-tab {{$activeTable === 'categories' ? 'active' : ''}}" href="/admin/categories">
                    Event Categories
                </a>
                <a id="issues-tab" class="col-3 col-md-12 container admin-tab {{$activeTable === 'issues' ? 'active' : ''}}" href="/admin/issues">
                    Issues
                </a>
            </div>
        </div>
        @yield('table')
    </div>
</div>

@endsection