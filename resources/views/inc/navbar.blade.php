<nav class="navbar navbar-expand-lg font-title">
    <div class="container">
        <a class="navbar-brand" href="/index_lin.html">Eventually</a>

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
                    <a class="nav-link" href="/create_event.html">Host</a>
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
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/user_dashboard.html">
                        John Doe
                    </a>
                </li>
                <li class="nav-item">
                    <div title="Notifications" class="nav-link notifications-item" data-toggle="modal"
                        data-target="#notifications_modal">
                        <i class="fas fa-bell nav-item-icon"></i>
                        <span class="nav-item-label">Notifications</span>
                    </div>
                </li>
                <li class="nav-item">
                    <div title="Submit an Issue" class="nav-link issues-item" data-toggle="modal"
                        data-target="#submit-issue-modal">
                        <i class="fas fa-exclamation-triangle nav-item-icon"></i>
                        <span class="nav-item-label">Submit an Issue</span>
                    </div>
                </li>
                <li class="nav-item">
                    <a title="Administration Dashboard" class="nav-link" href="/admin_dashboard.html">
                        <i class="fas fa-clipboard-list nav-item-icon"></i>
                        <span class="nav-item-label">Administration Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a title="Exit" class="nav-link" href="/">
                        <i class="fas fa-sign-out-alt nav-item-icon"></i>
                        <span class="nav-item-label">Exit</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div id="submit-issue-modal" class="modal fade font-content" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title custom-modal-title">Submit an Issue</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form>
                <div class="modal-body">
                    <textarea name="announcement-content" placeholder="Tell us what's wrong ..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn publish-button" data-dismiss="modal">Submit</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="notifications_modal" class="modal fade font-content" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title custom-modal-title">Notifications</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body container notifications-list">

                <div class="row notification-item" data-notif-id="1">
                    <div class="col-12">
                        <div class="row header">
                            <a class="col-8 col-md-10 title font-title" href="/event_page_lin.html">
                                Invitation to attend
                            </a>
                            <div class="col-4 col-md-2 ml-auto actions d-flex justify-content-end">
                                <div class="row">
                                    <span class="col-6 read">
                                        <i class="fas fa-eye"
                                            onclick="$('.notification-item[data-notif-id=\'1\'').removeClass('unread')"></i>
                                        <i class="fas fa-eye-slash"
                                            onclick="$('.notification-item[data-notif-id=\'1\'').addClass('unread')"></i>
                                    </span>
                                    <span class="col-6 delete">
                                        <i class="fas fa-times"
                                            onclick="$('.notification-item[data-notif-id=\'1\'').remove()"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 description">
                                You have been invited to attend Semana de Informática 2019
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row notification-item unread" data-notif-id="2">
                    <div class="col-12">
                        <div class="row header">
                            <a class="col-8 col-md-10 title font-title" href="/event_page_cancelled.html">
                                Event Cancelled
                            </a>
                            <div class="col-4 col-md-2 ml-auto actions d-flex justify-content-end">
                                <div class="row">
                                    <span class="col-6 read">
                                        <i class="fas fa-eye"
                                            onclick="$('.notification-item[data-notif-id=\'2\'').removeClass('unread')"></i>
                                        <i class="fas fa-eye-slash"
                                            onclick="$('.notification-item[data-notif-id=\'2\'').addClass('unread')"></i>
                                    </span>
                                    <span class="col-6 delete">
                                        <i class="fas fa-times"
                                            onclick="$('.notification-item[data-notif-id=\'2\'').remove()"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 description">
                                IEEE Code Week 2019 has been cancelled
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row notification-item" data-notif-id="3">
                    <div class="col-12">
                        <div class="row header">
                            <a class="col-8 col-md-10 title font-title" href="/event_page_lin.html">
                                Removed from Event
                            </a>
                            <div class="col-4 col-md-2 ml-auto actions d-flex justify-content-end">
                                <div class="row">
                                    <span class="col-6 read">
                                        <i class="fas fa-eye"
                                            onclick="$('.notification-item[data-notif-id=\'3\'').removeClass('unread')"></i>
                                        <i class="fas fa-eye-slash"
                                            onclick="$('.notification-item[data-notif-id=\'3\'').addClass('unread')"></i>
                                    </span>
                                    <span class="col-6 delete">
                                        <i class="fas fa-times"
                                            onclick="$('.notification-item[data-notif-id=\'3\'').remove()"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 description">
                                You have been removed from Eventually Organizers Day 2019
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row notification-item" data-notif-id="4">
                    <div class="col-12">
                        <div class="row header">
                            <a class="col-8 col-md-10 title font-title" href="/event_page_disabled.html">
                                Event Disabled
                            </a>
                            <div class="col-4 col-md-2 ml-auto actions d-flex justify-content-end">
                                <div class="row">
                                    <span class="col-6 read">
                                        <i class="fas fa-eye"
                                            onclick="$('.notification-item[data-notif-id=\'4\'').removeClass('unread')"></i>
                                        <i class="fas fa-eye-slash"
                                            onclick="$('.notification-item[data-notif-id=\'4\'').addClass('unread')"></i>
                                    </span>
                                    <span class="col-6 delete">
                                        <i class="fas fa-times"
                                            onclick="$('.notification-item[data-notif-id=\'4\'').remove()"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 description">
                                Fishing Workshop - HDNV Student Association has been disabled by a Platform
                                Administrator
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>