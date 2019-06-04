@extends('layouts.app')

@section('asset_includes')
<link href="{{ asset('css/settings.css') }}" rel="stylesheet">
<script src="{{asset('js/settings.js')}}" defer></script>
@endsection

@section('title', 'Notifications - Eventually')

@section('content')
    <div id="background_wave"></div>

    <div id="page-card" class="container card-container font-content settings-container">
        <header>
            <h1>User Settings</h1>
        </header>
        <form id="change-name-form" novalidate class="needs-validation" action="#">
            {{ csrf_field() }}
            <div novalidate class="needs-validation">
                <div class="form-group">
                    <input type="text" name="name" autocomplete="off" placeholder="New Name" required class="form-control">
                    <div class="invalid-feedback">Please a valid new name</div>
                </div>
                <button type="submit" class="btn change-button change-name-button">Change Name</button>
                <div class="status-messages">
                    <div class="alert alert-danger" style="display:none;white-space:pre-line"></div>
                    <div class="alert alert-success" style="display:none;white-space:pre-line"></div>
                </div>
            </div>
        </form>
        <form id="change-password-form" novalidate class="needs-validation" method="POST" action="/password/change">
            {{ csrf_field() }}
            <div class="form-group">
                <input type="password" name="current_password" placeholder="Current Password" required class="form-control">
                <div class="invalid-feedback">Please provide a valid password</div>
            </div>
            <div class="form-group">
                <input type="password" name="new_password" placeholder="New Password" required class="form-control">
                <div class="invalid-feedback">Please provide a valid new password</div>
            </div>
            <div class="form-group">
                <input type="password" name="new_password_confirmation" placeholder="Confirm New Password" required class="form-control">
                <div class="invalid-feedback">Please type your new password again</div>
            </div>
            <button type="submit" class="btn change-button change-password-button">Change Password</button>
            <div class="status-messages">
                <div class="alert alert-danger" style="display:none;white-space:pre-line"></div>
                <div class="alert alert-success" style="display:none;white-space:pre-line"></div>
            </div>
            @if (Session::has('error'))
                <div class="alert alert-danger">{{ Session::get('error') }}</div>
            @endif
            @if (Session::has('success'))
                <div class="alert alert-success">{{ Session::get('success') }}</div>
            @endif
        </form>
        <button type="button" class="btn btn-danger delete-account-button">Delete Account</button>
    </div>

@endsection