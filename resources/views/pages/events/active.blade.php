@extends('layouts.app')

@section('css_includes')
<link href="{{ asset('css/event_page.css') }}" rel="stylesheet">
@endsection

@section('title', $event->title . ' - Eventually')

@section('content')
    <div id="background_wave"></div>

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
                <h6>Event hosted by {{$owner->name}}</h6>
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
                        </span> {{$event->formatted_start_timestamp}}
                    </div>
                    @if(!empty($event->end_timestamp))
                    <div class="col-12 col-md-auto">
                        <span>
                            <i class="fas fa-minus icon-left"></i>
                            </i>
                        </span> {{$event->formatted_end_timestamp}}
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
                        {{$category->name}}
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
                        aria-selected="true">Announcements</a>
                    <a class="nav-item nav-link" id="nav-discussion-tab" data-toggle="tab" href="#nav-discussion"
                        role="tab" aria-controls="nav-discussion" aria-selected="false">Discussion</a>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-announcements" role="tabpanel"
                    aria-labelledby="nav-announcements-tab">
                    <div class="announcements-area">
                        @if(count($announcements) > 0)
                            @foreach ($announcements as $announcement)
                            <div class="announcement">
                                <div class="icon">
                                    <span>
                                        <i class="fas fa-info-circle"></i>
                                    </span>
                                </div>
                                <div class="content">
                                    <div class="date">
                                        {{$announcement->formatted_timestamp}}
                                    </div>
                                    <div class="text">
                                        {{$announcement->content}}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            No announcements yet!
                        @endif
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
                        @if(count($discussions) > 0)
                            @foreach ($discussions as $discussion_key => $discussion)
                            <div class="post">
                                <div class="icon">
                                    <span>
                                        <i class="fas fa-reply"></i>
                                    </span>
                                </div>
                                <div class="content">
                                    <header>
                                        <div>
                                            <div class="name">
                                                {{$discussion->creator->name}}
                                            </div>
                                            <div class="date">
                                                {{$discussion->formatted_timestamp}}
                                            </div>
                                        </div>
                                        <div class="text rating">
                                            <i class="fas fa-chevron-down"></i>
                                            {{$discussion->rating}}
                                            <i class="fas fa-chevron-up"></i>
                                        </div>
                                    </header>
                                    <div class="text">
                                        {{$discussion->content}}
                                    </div>
                                    <a class="comments-toggler" data-toggle="collapse" href="#comments_section_{{$discussion_key}}"
                                        role="button" aria-expanded="false" aria-controls="comments_section_{{$discussion_key}}">
                                        {{$discussion->num_comments}} comments
                                    </a>
                                    <div class="collapse" id="comments_section_{{$discussion_key}}">
                                        @if(count($discussion_comments[$discussion_key]) > 0)
                                            @foreach($discussion_comments[$discussion_key] as $comment)
                                            <div class="comment">
                                                <div class="name">
                                                    {{$comment->creator->name}}
                                                </div>
                                                <div class="date">
                                                    {{$comment->formatted_timestamp}}
                                                </div>
                                                <div class="text">
                                                    {{$comment->content}}
                                                </div>
                                            </div>
                                            @endforeach
                                        @else
                                            No comments yet. Would you like to add one?
                                        @endif
                                        <div class="add-comment">
                                            <textarea name="comment" placeholder="Add a comment..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            No discussion posts yet!
                        @endif
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