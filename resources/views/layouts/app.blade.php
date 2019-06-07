<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @section('open-graph-title')
    <meta property="og:title" content="Eventually - Create. Attend. Organize.">
    @endsection
    <meta property="og:site_name" content="Eventually">
    <meta property="og:description" content="Create amazing experiences for everyone. Attend your favorite events. Organize easily and collaboratively.">
    <meta property="og:type" content="Events">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>
    <script src="{{ asset('bootstrap/jquery-3.3.1.min.js') }}" defer></script>
    <script src="{{ asset('bootstrap/popper.min.js') }}" defer></script>
    <script src="{{ asset('bootstrap/js/bootstrap.min.js') }}" defer></script>
    <script src="{{ asset('js/form_validation.js') }}" defer></script>
    <script src="{{ asset('js/moment.js/moment-with-locales.js') }}" defer></script>
    <script src="{{ asset('js/popovers.js') }}" defer></script>
    <script src="{{ asset('js/tempus_dominus/index.js') }}" defer></script>
    
    <script src="{{ asset('js/modals.js') }}" defer></script>

    @yield('scripts')
    

    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('fonts/Fonts.css') }}" rel="stylesheet">
    <link href="{{ asset('fontawesome/css/all.css') }}" rel="stylesheet">

    <link href="{{ asset('css/constants.css') }}" rel="stylesheet">
    <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/common.css') }}" rel="stylesheet">
    <link href="{{ asset('css/print.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/css/tempusdominus-bootstrap-4.min.css" />
    @yield('asset_includes')
</head>

<body>
    <header>
        @if(empty($hideNavbar))
            @include('inc.navbar')
        @endif
    </header>

    <noscript>
        <span class="warning">Warning:&nbsp;</span><i>JavaScript</i> is currently disabled and is required to fully experience this website
    </noscript>
    
    
    @yield('content')
    
    
    @if(empty($hideFooter))
        @include('inc.footer')
    @endif

    <div class="modal fade font-content" tabindex="-1" role="dialog" aria-labelledby="confirmationModal" aria-hidden="true" id="confirmation-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="confirmation-modal-text">Confirmar</h4>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-default" id="confirmation-modal-yes">Yes</button>
                <button type="button" class="btn btn-primary" id="confirmation-modal-no">No</button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>

