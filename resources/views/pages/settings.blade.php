@extends('layouts.app')

@section('asset_includes')
<link href="{{ asset('css/settings.css') }}" rel="stylesheet">
<script src="{{asset('js/settings.js')}}" defer></script>
@endsection

@section('title', 'Settings - Eventually')

@section('content')
<div id="background_wave"></div>

<div id="page-card" class="container card-container font-content settings-container">
    <header>
        <h1>User Settings</h1>
        <i class="fas fa-question-circle form-info" data-toggle="popover" data-placement="top" data-content="In this page, you may edit your personal data, such as your user name and password."></i>
    </header>
    <form id="change-name-form" novalidate class="needs-validation" action="#">
        <fieldset>
        <legend style="display:none;">Change name form</legend>
        {{ csrf_field() }}
            <div novalidate class="needs-validation">
                <div class="form-group">
                    <input type="text" name="name" autocomplete="off" placeholder="New Name" aria-label="New Name" required class="form-control">
                    <div class="invalid-feedback">Please a valid new name</div>
                </div>
                <button type="submit" class="btn change-button change-name-button">Change Name</button>
                <div class="status-messages">
                    <div class="alert alert-danger" style="display:none;white-space:pre-line"></div>
                    <div class="alert alert-success" style="display:none;white-space:pre-line"></div>
                </div>
            </div>
        </fieldset>
    </form>
    <form id="change-password-form" novalidate class="needs-validation" method="POST" action="/password/change">
        <fieldset>
            <legend style="display:none;">Change password form</legend>
            {{ csrf_field() }}
            <div class="form-group">
                <input type="password" name="current_password" placeholder="Current Password" aria-label="Current Password" required class="form-control">
                <div class="invalid-feedback">Please provide a valid password</div>
            </div>
            <div class="form-group">
                <input type="password" name="new_password" placeholder="New Password" aria-label="New Password" required class="form-control">
                <div class="invalid-feedback">Please provide a valid new password</div>
            </div>
            <div class="form-group">
                <input type="password" name="new_password_confirmation" placeholder="Confirm New Password" aria-label="Confirm New Password" required class="form-control">
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
        </fieldset>
    </form>
    <button type="button" class="btn btn-danger delete-account-button" data-toggle="modal" data-target="#delete-account-modal">Delete Account</button>
</div>

<div id="delete-account-modal" class="modal fade font-content" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="header-container">
                    <div class="modal-title custom-modal-title">Delete Account</div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="account">
                <fieldset>
                <legend style="display:none;">Delete account form</legend>
                    {{ csrf_field() }}
                    @method('DELETE')
                    <div class="modal-body">                 
                        Are you sure you want to <span class="delete-text">delete</span> your account? This action is <strong>permanent</strong> and you will not be able to restore it.
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Delete</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</div>

@endsection