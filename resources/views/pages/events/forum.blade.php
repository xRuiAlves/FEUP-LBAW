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