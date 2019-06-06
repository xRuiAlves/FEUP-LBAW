<div class="row">
    <div class="col-12 message-area">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a class="nav-item nav-link active" id="announcements-section-tab" data-toggle="tab"
                    href="#announcements-section" role="tab" aria-controls="announcements-section"
                    aria-selected="true">Announcements</a>
                <a class="nav-item nav-link" id="discussion-section-tab" data-toggle="tab" href="#discussion-section"
                    role="tab" aria-controls="discussion-section" aria-selected="false">Discussion</a>
            </div>
        </nav>
        <div id="forum-status-messages" class="status-messages">
            <div class="alert alert-danger" style="display:none;white-space:pre-line"></div>
            <div class="alert alert-success" style="display:none;white-space:pre-line"></div>
        </div>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="announcements-section" role="tabpanel"
                aria-labelledby="announcements-section-tab">
                    @if(count($announcements) > 0)
                    @if($is_organizer)
                    <button type="submit" class="btn create-post-button" data-toggle="modal" data-target="#create-announcement-modal">
                        <span>
                            <i class="far fa-edit"></i>
                        </span>
                        Create Announcement
                    </button>
                    @endif
                    <div class="announcements-area">
                        @foreach ($announcements as $announcement)
                        <div class="announcement" data-announcement-id={{$announcement->id}}>
                            <div class="content">
                                <div class="date">
                                    {{$announcement->formatted_timestamp}}h
                                </div>
                                <div class="text">
                                    {{$announcement->content}}
                                </div>
                            </div>
                            @if($is_organizer || (Auth::check() && Auth::user()->is_admin))
                            <div class="delete-post-icon">
                                <i class="fas fa-trash-alt" title="Delete announcement" data-toggle="modal" data-target="#delete-announcement-modal"></i>
                            </div>
                            @endif
                        </div>
                        @endforeach
                        <div class="pagination-container">
                            {{$announcements->appends(['discussions' => $discussions->currentPage()])->fragment('announcements-section')->links("pagination::bootstrap-4")}}
                        </div>
                    </div>
                    @else
                    <div class="no-posts">
                        <div class="description">
                            The <strong>event organizers</strong> haven't posted any announcements yet!
                        </div>
                        <div class="button-container">
                            @if(Auth::check())
                                <button type="submit" class="btn create-post-button no-posts-button" data-toggle="modal" data-target="#create-announcement-modal">
                                    <span>
                                        <i class="far fa-edit"></i>
                                    </span>
                                    Create Announcement
                                </button>
                            @endif
                        </div>
                    </div>
                    @endif
            </div>
            <div class="tab-pane fade" id="discussion-section" role="tabpanel" aria-labelledby="discussion-section-tab">
                    @if(count($discussions) > 0)
                        @if(Auth::check())
                        <button type="submit" class="btn create-post-button" data-toggle="modal" data-target="#create-post-modal">
                            <span>
                                <i class="far fa-edit"></i>
                            </span>
                            Create Post
                        </button>
                        @endif
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
                                    <div style="display:flex;">
                                        <div class="text rating">
                                            @if(Auth::check())
                                                <i class="fas fa-chevron-up upvote"></i>
                                            @endif
                                            <div>
                                                @if(Auth::guest())
                                                    Rating:
                                                @endif
                                                <span class="rating-value">{{$discussion->rating}}</span>
                                            </div>
                                            @if(Auth::check())
                                                <i class="fas fa-chevron-down downvote"></i>
                                            @endif
                                        </div>
                                        @if($is_organizer || (Auth::check() && Auth::user()->is_admin))
                                        <div class="delete-post-icon">
                                            <i class="fas fa-trash-alt" title="Delete post" data-toggle="modal" data-target="#delete-post-modal"></i>
                                        </div>
                                        @endif
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
                                            <fieldset>
                                            <legend style="display:none;">Create comment form</legend>
                                            {{ csrf_field() }}
                                            <div class="row no-gutters">
                                                <div class="col-12 status-messages">
                                                    <div class="alert alert-danger" style="display:none;white-space:pre-line"></div>
                                                    <div class="alert alert-success" style="display:none;white-space:pre-line"></div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <textarea name="comment" placeholder="Add a comment..." required class="form-control" aria-label="Comment"></textarea>
                                                <div class="invalid-feedback">Please provide the comment content.</div>
                                            </div>
                                            <button type="submit" class="btn publish-button submit-comment">Create comment</button>
                                        </fieldset>
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
                        <div class="pagination-container">
                            {{$discussions->appends(['announcements' => $announcements->currentPage()])->fragment('discussion-section')->links("pagination::bootstrap-4")}}
                        </div>
                    @else
                        <div class="no-posts">
                            <div class="description">
                                No one has created a <strong>post</strong> on this event
                            </div>
                            <div class="button-container">
                                @if(Auth::check())
                                    <button type="submit" class="btn create-post-button no-posts-button" data-toggle="modal" data-target="#create-post-modal">
                                        <span>
                                            <i class="far fa-edit"></i>
                                        </span>
                                        Create Post
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div id="create-post-modal" class="modal fade font-content" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="header-container">
                    <div class="modal-title custom-modal-title">Create a new Post</div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="create-post-form" novalidate class="needs-validation">
            <fieldset>
                <legend style="display:none;">Create post form</legend>
                {{ csrf_field() }}
                <div class="modal-body">                 
                    <div class="form-group">
                        <textarea required class="form-control" name="content" placeholder="Enter the post content..." aria-label="Post Content"></textarea>
                        <div class="invalid-feedback">Please provide the post content</div>
                    </div>
                </div>
                <div class="status-messages">
                    <div class="alert alert-danger" style="display:none;white-space:pre-line"></div>
                    <div class="alert alert-success" style="display:none;white-space:pre-line"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn publish-button solve-issue">Create</button>
                    <button type="button" class="btn btn-secondary close-button" data-dismiss="modal">Close</button>
                </div>
            </fieldset>
            </form>
        </div>
    </div>
</div>

<div id="create-announcement-modal" class="modal fade font-content" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="header-container">
                    <div class="modal-title custom-modal-title">Create a new announcement</div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="create-announcement-form" novalidate class="needs-validation">
            <fieldset>
                <legend style="display:none;">Create announcement form</legend>
                {{ csrf_field() }}
                <div class="modal-body">                 
                    <div class="form-group">
                        <textarea required class="form-control" name="content" placeholder="Enter the announcement content..." aria-label="Announcement Content"></textarea>
                        <div class="invalid-feedback">Please provide the announcement content</div>
                    </div>
                </div>
                <div class="status-messages">
                    <div class="alert alert-danger" style="display:none;white-space:pre-line"></div>
                    <div class="alert alert-success" style="display:none;white-space:pre-line"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn publish-button solve-issue">Create</button>
                    <button type="button" class="btn btn-secondary close-button" data-dismiss="modal">Close</button>
                </div>
            </fieldset>
            </form>
        </div>
    </div>
</div>

<div id="delete-post-modal" class="modal fade font-content" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="header-container">
                    <div class="modal-title custom-modal-title">Delete post</div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">                 
                Are you sure you want to delete this post?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger delete-post">Delete</button>
                <button type="button" class="btn btn-secondary close-button" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="delete-announcement-modal" class="modal fade font-content" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="header-container">
                    <div class="modal-title custom-modal-title">Delete announcement</div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">                 
                Are you sure you want to delete this announcement?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger delete-announcement">Delete</button>
                <button type="button" class="btn btn-secondary close-button" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>