@extends('layouts.app')

@section('title', 'Password Reset - Eventually')

@section('content')
<div id="background_wave"></div>

<div id="page-card" class="container card-container font-content login-register">
    <header class="d-flex align-items-center">
        <div class="modal-title custom-modal-title font-title d-inline-block">Reset Password</div>
        <i class="fas fa-question-circle form-info" data-toggle="popover" data-placement="top" data-content="In this page you may reset your account's password in case you forgot it."></i>
    </header>
    <div class="modal-body">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <form class="needs-validation font-content" novalidate method="POST" action="{{route('password.email')}}">
        <fieldset>
            <legend style="display:none;">Reset password form</legend>
            {{ csrf_field() }}
            <div class="form-group">
                <input type="email" name="email" class="form-control" value="{{old('email')}}" placeholder="email" aria-label="Email" required autofocus>
                @if ($errors->has('email'))
                    <span class="error">
                    {{ $errors->first('email') }}
                    </span>
                @endif
                <div class="invalid-feedback">
                    Please provide a valid email address
                </div>
            </div>

            <div class="form-group">
                <div class="d-flex justify-content-center">
                    <button type="submit" class="my-btn my-btn-primary">
                        Send Password Reset Link
                    </button>
                </div>
            </div>
        </fieldset>
        </form>
        <div class="modal-footer">
            <span>Don't remember which email you used? Contact us at <a href="mailto:help@eventual.ly">help@eventual.ly</a></span>
        </div>
    </div>
</div>
@endsection
