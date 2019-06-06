@extends('layouts.app')

@section('title', 'Register - Eventually')

@section('content')
<div id="background_wave"></div>

<div id="page-card" class="container card-container font-content login-register">
  <header>
    <div class="modal-title custom-modal-title font-title">Register</div>
  </header>
  <div class="modal-body">
      <form method="POST" action="{{route('register')}}" novalidate class="needs-validation font-content">
        <fieldset>
        <legend style="display:none;">Register account form</legend>
        {{ csrf_field() }}
        <div class="form-group">
          <input type="text" name="name" value="{{ old('name') }}" placeholder="name" aria-label="Name" required autofocus class="form-control">
            @if ($errors->has('name'))
            <span class="error">
                {{ $errors->first('name') }}
            </span>
            @endif
            <div class="invalid-feedback">
              Please provide a name
            </div>
        </div>
        <div class="form-group">
            <input type="email" name="email" value="{{ old('email') }}" placeholder="email" aria-label="Email" required class="form-control">
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
              Please provide a valid password
          </div>
        </div>
        <div class="form-group">
          <input type="password" name="password_confirmation" placeholder="confirm password" aria-label="Password Confirmation" required class="form-control">
          <div class="invalid-feedback">
              Please type your password again
          </div>
        </div>
        <div class="d-flex justify-content-center">
            <button class="my-btn my-btn-primary" type="submit">Register</button>
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
      <span>Already have an account? Login <a href="{{route('login')}}">here!</a></span>
  </div>
</div>
@endsection