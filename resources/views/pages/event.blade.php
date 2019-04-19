@extends('layouts.app')

@section('css_includes')
<link href="{{ asset('css/event_page.css') }}" rel="stylesheet">
@endsection

@section('title', 'Event Page (WIP)')

@section('content')
<div class="regular-wave" id="background_wave"></div>

<div id="page-card" class="container card-container font-content event-card">
    <div class="event-brief">
        <div class="row no-gutters main">
            <div class="col-12 col-lg-9 event-title font-title">
                <h1>{{$event->title}}</h1>
            </div>
            <div class="col-12 col-lg-3 attend-btn alone-right">
                {{-- Todo: Ifs here for authenticated/owner/etc? --}}
                <button type="button" class="btn" data-toggle="modal" data-target="#login_modal">
                    <span>
                        <i class="fas fa-calendar-check icon-left"></i>
                    </span>
                    Attend
                </button>
            </div>
            <div class="col-12 hosted-by-label mt-2 mt-lg-0">
                <h6>Event hosted by TODO</h6>
            </div>
        </div>
        <div class="mobile-wave" id="background_wave"></div>
        <div class="separator main-separator">
            <hr>
        </div>
        <div class="row event-details font-title">
            <div class="col-6 event-spacetime">
                <div class="row mb-1 no-gutters date">
                    <div class="col-12 col-md-auto">
                        <span>
                            <i class="far fa-calendar-alt icon-left"></i>
                        </span> {{$event->start_timestamp}}
                    </div>
                    @if(!empty($event->end_timestamp))
                    <div class="col-12 col-md-auto">
                        <span>
                            <i class="fas fa-minus icon-left"></i>
                            </i>
                        </span> {{$event->end_timestamp}}
                    </div>
                    @endif
                </div>
                @if(!empty($event->location))
                <div class="row">
                    <div class="col-12">
                        <span>
                            <i class="fas fa-map-marker-alt icon-left"></i>
                        </span> {{$event->location}}
                    </div>
                </div>
                @endif
            </div>
            <div class="col-6 event-category">
                <div class="row mb-1">
                    <div class="col-12">
                        @if($event->price == 0)
                        Free
                        @else
                        {{$event->price}} €
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        Technology (TODO)
                        <span>
                            <i class="fas fa-tag icon-right"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row event-description">
            <div class="col-12">
                <p>{{$event->description}}</p>
            </div>
        </div>
        <div class="separator">
            <hr>
        </div>
    </div>
    <div class="row no-gutters event-map">
        <div class="col-12">
            <iframe class="event-map"
                src="https://maps.google.com/?q={{$event->latitude}},{{$event->longitude}}&amp;ie=UTF8&amp;t=&amp;z=14&amp;iwloc=B&amp;output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0">
            </iframe>
        </div>
    </div>
    <div class="row">
        <div class="col-12 message-area">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-announcements-tab" data-toggle="tab"
                        href="#nav-announcements" role="tab" aria-controls="nav-announcements"
                        aria-selected="true">Announcements (TODO)</a>
                    <a class="nav-item nav-link" id="nav-discussion-tab" data-toggle="tab" href="#nav-discussion"
                        role="tab" aria-controls="nav-discussion" aria-selected="false">Discussion (TODO)</a>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-announcements" role="tabpanel"
                    aria-labelledby="nav-announcements-tab">
                    <div class="announcements-area">

                        <div class="announcement">
                            <div class="icon">
                                <span>
                                    <i class="fas fa-info-circle"></i>
                                </span>
                            </div>
                            <div class="content">
                                <div class="date">
                                    14.02.2019 12:12
                                </div>
                                <div class="text">
                                    Dear Semana de Informática 2019 attendees, we remind you all the importance of
                                    bringing your laptop to the workshops, so
                                    that you can take the most out of this experience. We also remind you to be near
                                    the workshop room about 5 minutes before the start, so that we can deal with the
                                    check-in logistics on time.
                                </div>
                            </div>
                        </div>
                        <div class="announcement">
                            <div class="icon">
                                <span>
                                    <i class="fas fa-info-circle"></i>
                                </span>
                            </div>
                            <div class="content">
                                <div class="date">
                                    12.04.2019 18:14
                                </div>
                                <div class="text">
                                    We are glad to inform that the attendance limit has reached 95%, which makes
                                    this year's edition the one with the most participants.
                                    Be sure to get your ticket before they are all sold out!
                                </div>
                            </div>
                        </div>
                        <div class="announcement">
                            <div class="icon">
                                <span>
                                    <i class="fas fa-info-circle"></i>
                                </span>
                            </div>
                            <div class="content">
                                <div class="date">
                                    08.11.2012 21:47
                                </div>
                                <div class="text">
                                    The event's program is finally completed! Be sure to visit our website
                                    (www.sinf.pt) to learn more and don't forget to get
                                    your ticket!
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-discussion" role="tabpanel" aria-labelledby="nav-discussion-tab">
                    <div class="discussions-area">
                        <div class="add-btn" data-toggle="modal" data-target="#login_modal">
                            <span>
                                <i class="far fa-edit"></i>
                            </span>
                            Create Post
                        </div>
                        <div class="post">
                            <div class="icon">
                                <span>
                                    <i class="fas fa-reply"></i>
                                </span>
                            </div>
                            <div class="content">
                                <div class="name">
                                    A User's Name
                                </div>
                                <div class="date">
                                    08.04.2019 14:21
                                </div>
                                <div class="text">
                                    Really glad that Núcleo de Informática (NIAEFEUP) is responsible of organizing
                                    this year's edition! The last edition was
                                    a lot of fun, and I'm sure they'll have a lot of great surprises expecting us.
                                </div>
                                <a class="comments-toggler" data-toggle="collapse" href="#comments_section_1"
                                    role="button" aria-expanded="false" aria-controls="comments_section_1">
                                    2 comments
                                </a>
                                <div class="collapse" id="comments_section_1">
                                    <div class="comment">
                                        <div class="name">
                                            Random User's Name
                                        </div>
                                        <div class="date">
                                            08.04.2019 14:22
                                        </div>
                                        <div class="text">
                                            Really glad, and now I'm just going to write a super long comment for no
                                            reason at all, just so I can test this UI thoroughly. And it seems the
                                            last sentence was not enough so I just added this one.
                                        </div>

                                    </div>
                                    <div class="comment">
                                        <div class="name">
                                            Another User's Name
                                        </div>
                                        <div class="date">
                                            08.04.2019 14:23
                                        </div>
                                        <div class="text">
                                            Really glad, and now I'm just going to write a super long comment for no
                                            reason at all, just so I can test this UI thoroughly. And it seems the
                                            last sentence was not enough so I just added this one.
                                        </div>

                                    </div>
                                    <div class="add-comment">
                                        <textarea name="comment" placeholder="Add a comment..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="post">
                            <div class="icon">
                                <span>
                                    <i class="fas fa-reply"></i>
                                </span>
                            </div>
                            <div class="content">
                                <div class="name">
                                    A User's Name
                                </div>
                                <div class="date">
                                    02.04.2019 08:12
                                </div>
                                <div class="text">
                                    The amount of workshops that are taking place is actually amazing! As an
                                    attendee, may I attend whichever workshops I desire,
                                    or do I have some kind of limitation? Can't wait for this year's edition!
                                </div>
                                <a class="comments-toggler" data-toggle="collapse" href="#comments_section_2"
                                    role="button" aria-expanded="false" aria-controls="comments_section_2">
                                    2 comments
                                </a>
                                <div class="collapse" id="comments_section_2">
                                    <div class="comment">
                                        <div class="name">
                                            Random User's Name
                                        </div>
                                        <div class="date">
                                            08.04.2019 14:22
                                        </div>
                                        <div class="text">
                                            Really glad, and now I'm just going to write a super long comment for no
                                            reason at all, just so I can test this UI thoroughly. And it seems the
                                            last sentence was not enough so I just added this one.
                                        </div>

                                    </div>
                                    <div class="comment">
                                        <div class="name">
                                            Another User's Name
                                        </div>
                                        <div class="date">
                                            08.04.2019 14:23
                                        </div>
                                        <div class="text">
                                            Really glad, and now I'm just going to write a super long comment for no
                                            reason at all, just so I can test this UI thoroughly. And it seems the
                                            last sentence was not enough so I just added this one.
                                        </div>

                                    </div>
                                    <div class="add-comment">
                                        <textarea name="comment" placeholder="Add a comment..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="post">
                            <div class="icon">
                                <span>
                                    <i class="fas fa-reply"></i>
                                    </i>
                                </span>
                            </div>
                            <div class="content">
                                <div class="name">
                                    A User's Name
                                </div>
                                <div class="date">
                                    30.03.2019 13:30
                                </div>
                                <div class="text">
                                    Well, this year's ticketing option seems like a much better aproach than
                                    Eventbrite.
                                </div>
                                <a class="comments-toggler" data-toggle="collapse" href="#comments_section_3"
                                    role="button" aria-expanded="false" aria-controls="comments_section_3">
                                    2 comments
                                </a>
                                <div class="collapse" id="comments_section_3">
                                    <div class="comment">
                                        <div class="name">
                                            Random User's Name
                                        </div>
                                        <div class="date">
                                            08.04.2019 14:22
                                        </div>
                                        <div class="text">
                                            Really glad, and now I'm just going to write a super long comment for no
                                            reason at all, just so I can test this UI thoroughly. And it seems the
                                            last sentence was not enough so I just added this one.
                                        </div>

                                    </div>
                                    <div class="comment">
                                        <div class="name">
                                            Another User's Name
                                        </div>
                                        <div class="date">
                                            08.04.2019 14:23
                                        </div>
                                        <div class="text">
                                            Really glad, and now I'm just going to write a super long comment for no
                                            reason at all, just so I can test this UI thoroughly. And it seems the
                                            last sentence was not enough so I just added this one.
                                        </div>

                                    </div>
                                    <div class="add-comment">
                                        <textarea name="comment" placeholder="Add a comment..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div id="create-post-modal" class="modal fade font-content" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title custom-modal-title">Create Post</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form>
                <div class="modal-body">
                    <textarea name="announcement-content" placeholder="Post content ..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn publish-button" data-dismiss="modal">Create</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="attend-event-modal" class="modal fade font-content" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title custom-modal-title">Ticket</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Do you wish to obtain a free ticket to Semana de Informática 2019?
            </div>
            <div class="modal-footer">
                <a href="/event_page_attendee.html" class="btn publish-button">Confirm</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection