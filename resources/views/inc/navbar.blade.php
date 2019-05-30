<nav class="navbar navbar-expand-lg font-title" id="navbar">
    <div class="container">
        <a class="navbar-brand" href="/">Eventually</a>

        <button class="navbar-toggler" type="button" data-toggle="collapse"
            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon">
                <i class="fas fa-bars"></i>
            </span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav nav-highlighted mr-auto">
                <li class="nav-item search-mobile">
                    <a class="nav-link" href="/#search-box-anchor">Search</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/event/create">Create Event</a>
                </li>

                <form class=" nav-item ml-2 my-2 my-lg-0">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search" aria-label="Search"
                            aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn submit-search" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </ul>
            @if(Auth::guest())
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a href="{{route('login')}}" title="Log In" class="nav-link">
                        Login
                    </a>
                </li>
                <li class="nav-item">
                <a href="{{route('register')}}" title="Register" class="nav-link">
                        Register
                    </a>
                </li>
            </ul>
            @else
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{route('dashboard')}}">
                        {{Auth::user()->name}}
                    </a>
                </li>
                <li class="nav-item">
                    <a title="Notifications" class="nav-link notifications-item" href="{{route('notifications')}}">
                        <i class="fas fa-bell nav-item-icon"></i>
                        <span class="nav-item-label">Notifications</span>
                    </a>
                </li>
                <li class="nav-item">
                    <div title="Submit an Issue" class="nav-link issues-item" data-toggle="modal"
                        data-target="#submit-issue-modal">
                        <i class="fas fa-exclamation-triangle nav-item-icon"></i>
                        <span class="nav-item-label">Submit an Issue</span>
                    </div>
                </li>
                @if(Auth::user()->is_admin) {{-- TODO: Change this to use policies --}}
                <li class="nav-item">
                    <a title="Administration Dashboard" class="nav-link" href="{{route('admin')}}">
                        <i class="fas fa-clipboard-list nav-item-icon"></i>
                        <span class="nav-item-label">Administration Dashboard</span>
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    <a title="Exit" class="nav-link" href="{{route('logout')}}">
                        <i class="fas fa-sign-out-alt nav-item-icon"></i>
                        <span class="nav-item-label">Exit</span>
                    </a>
                </li>
            </ul>
            @endif
        </div>
    </div>
</nav>

<div id="submit-issue-modal" class="modal fade font-content" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="header-container">
                    <div class="modal-title custom-modal-title">Submit an Issue</div>
                    <i class="fas fa-question-circle form-info" data-toggle="popover" data-placement="top" data-content="This form allows you to contact the website administrators with any issue you might be experiencing."></i>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form novalidate class="needs-validation create-issue-form" action="/issue/create" method="post">
                {{ csrf_field() }}
                <div class="modal-body">                 
                    <div class="form-group">
                        <input class="form-control" required type="text" name="title" placeholder="Title" autocomplete="off" value="{{ old('price') }}"></input>
                        <div class="invalid-feedback">Please provide a title for the issue</div>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" required name="content" placeholder="Tell us what's wrong">{{ old('content') }}</textarea>
                        <div class="invalid-feedback">Please describe your issue</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn publish-button">Submit</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>