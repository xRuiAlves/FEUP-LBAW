@extends('layouts.app')

@section('title', 'Login - Eventually')

@section('content')
<div id="background_wave"></div>

<div id="page-card" class="container card-container font-content login-register">
    <header>
        <div class="modal-title custom-modal-title font-title">Login</div>
    </header>
    <div class="modal-body">
        <form method="POST" action="{{route('login')}}" novalidate class="needs-validation font-content">
        <fieldset>
            <legend style="display:none;">Login form</legend>
            {{ csrf_field() }}
            <div class="form-group">
                <input type="email" name="email" value="{{ old('email') }}" placeholder="email" aria-label="Email" required autofocus class="form-control">
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
                <input type="password" name="password" placeholder="password" aria-label="Password" required class="form-control">
                @if ($errors->has('password'))
                <span class="error">
                    {{ $errors->first('password') }}
                </span>
                @endif
                <div class="invalid-feedback">
                    Please type your password
                </div>
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Remember Me
                </label>
            </div>
            <div class="d-flex justify-content-center">
                <button class="my-btn my-btn-primary" type="submit">Login</button>
            </div>
        </fieldset>
        </form>
        <div class="d-flex justify-content-center">
            <a class="my-btn my-btn-borderless-secondary" href="/WIP">
                <span class="nav-icon icon-left">
                    <i class="fab fa-google" aria-hidden="true"></i>
                </span>
                Sign in with Google
            </a>
        </div>
    </div>
    <div class="modal-footer">
        <span>Don't have an account yet? Register <a href="{{route('register')}}">here!</a></span>
    </div>
</div>
@endsection