<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    {{-- <script type="text/javascript" src={{ asset('js/app.js') }} defer>
    </script> --}}

    <script type="text/javascript" src="{{ asset('bootstrap/jquery-3.3.1.min.js') }}" defer></script>
    <script type="text/javascript" src="{{ asset('bootstrap/popper.min.js') }}" defer></script>
    <script type="text/javascript" src="{{ asset('bootstrap/js/bootstrap.min.js') }}" defer></script>
    <script type="text/javascript" src="{{ asset('js/form_validation.js') }}" defer></script>
    <script type="text/javascript" src="{{ asset('js/moment.js/moment-with-locales.js') }}" defer></script>
    <script type="text/javascript" src="{{ asset('js/popovers.js') }}" defer></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/js/tempusdominus-bootstrap-4.min.js" defer></script>
    
    @yield('scripts')
    

    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('fonts/Fonts.css') }}" rel="stylesheet">
    <link href="{{ asset('fontawesome/css/all.css') }}" rel="stylesheet">

    <link href="{{ asset('css/constants.css') }}" rel="stylesheet">
    <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/common.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/css/tempusdominus-bootstrap-4.min.css" />
    @yield('asset_includes')
</head>

<body>
    <header>
        @if(empty($hideNavbar))
            @include('inc.navbar')
        @endif
    </header>
    
    
    @yield('content')
    
    
    @if(empty($hideFooter))
        @include('inc.footer')
    @endif

</body>

</html>

