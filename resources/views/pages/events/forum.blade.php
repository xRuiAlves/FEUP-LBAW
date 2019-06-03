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
                            <div class="content">
                                <div class="date">
                                    {{$announcement->formatted_timestamp}}h
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
                        <div class="post" data-post-id={{$discussion->id}}>
                            <div class="content">
                                <header>
                                    <div>
                                        <div class="name">
                                            {{$discussion->creator->name}}
                                        </div>
                                        <div class="date">
                                            {{$discussion->formatted_timestamp}}h
                                        </div>
                                    </div>
                                    <div class="text rating">
                                        <i class="fas fa-chevron-up upvote"></i>
                                        <span class="rating-value">{{$discussion->rating}}</span>
                                        <i class="fas fa-chevron-down downvote"></i>
                                    </div>
                                </header>
                                <div class="text">
                                    {{$discussion->content}}
                                </div>
                                <a class="comments-toggler" data-toggle="collapse" href="#comments_section_{{$discussion_key}}"
                                    role="button" aria-expanded="false" aria-controls="comments_section_{{$discussion_key}}">
                                    <span class="num-comments">{{$discussion->num_comments}}</span> comments
                                </a>
                                <div class="collapse" id="comments_section_{{$discussion_key}}">
                                    <div class="add-comment">
                                        <form class="needs-validation create-comment-form" novalidate action="#" data-post-id={{$discussion->id}}>
                                            {{ csrf_field() }}
                                            <div class="row no-gutters">
                                                <div class="status_messages" class="col-12">
                                                    <div class="alert alert-danger" style="display:none;white-space:pre-line"></div>
                                                    <div class="alert alert-success" style="display:none;white-space:pre-line"></div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <textarea name="comment" placeholder="Add a comment..." required class="form-control"></textarea>
                                                <div class="invalid-feedback">Please provide the comment content.</div>
                                            </div>
                                            <button type="submit" class="btn publish-button submit-comment">Create comment</button>
                                        </form>
                                    </div>
                                    <div class="comments-list">
                                        @if(count($discussion_comments[$discussion_key]) > 0)
                                            @foreach($discussion_comments[$discussion_key] as $comment)
                                            <div class="comment">
                                                <div class="name">
                                                    {{$comment->creator->name}}
                                                </div>
                                                <div class="date">
                                                    {{$comment->formatted_timestamp}}h
                                                </div>
                                                <div class="text">
                                                    {{$comment->content}}
                                                </div>
                                            </div>
                                            @endforeach
                                        @endif
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