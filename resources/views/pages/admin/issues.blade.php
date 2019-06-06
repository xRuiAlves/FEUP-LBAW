@extends('layouts.admin', ['activeTable' => 'issues'])

@section('asset_includes')
@parent
<script src="{{asset('js/admin_issues_page.js')}}" defer></script>
@endsection

@section('table')

<div id="issue-table" class="admin-dashboard col-12 col-md-10 col-xl-11">
    <div class="custom-title">Issues</div>
    <div class="searchbar-container">
        <form class="form-inline" action="" method="get">
        <fieldset>
            <legend style="display:none;">Search issues form</legend>
            <label class="sr-only" for="inlineFormInputName2">Search for Issues</label>
            <input type="text" class="form-control mb-2 mr-sm-2" id="inlineFormInputName2" placeholder="Ex: Ticket bug" name="search" aria-label="Search Issue">
            
            <button type="submit" class="btn btn-primary mb-2 fts-search-button">Search</button>
        </fieldset>
        </form>
    </div>
    <div class="row no-gutters">
        <div class="col-12 status-messages">
            <div class="alert alert-danger" style="display:none;white-space:pre-line"></div>
            <div class="alert alert-success" style="display:none;white-space:pre-line"></div>
        </div>
    </div>
    <div class="content-table">
        <table class="table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Title</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($issues as $issue)
                <tr class="issue-header" data-toggle="collapse" data-target="#issue{{$issue->id}}collapse" aria-expanded="false" aria-controls="issue{{$issue->id}}collapse" 
                data-issue-solved={{$issue->is_solved ? "1" : "0"}} data-issue-id={{$issue->id}} data-issue-creator-id={{$issue->creator_id}}>
                    <td>{{$issue->id}}</td>
                    <td>{{$issue->creator_name}}</td>
                    <td>{{$issue->date}}</td>
                    <td>{{$issue->time}}</td>
                    <td>{{$issue->title}}</td>
                    <td class="button-data-field">
                        @if($issue->is_solved) 
                            <button class="btn action-btn solve-issue-pop-modal solved-issue">Solved</button> 
                        @else 
                            <button class="btn action-btn solve-issue-pop-modal">Solve</button> 
                        @endif
                    </td>
                </tr>
                <tr class="collapse" id="issue{{$issue->id}}collapse">
                    <td colspan="6" class="no-borders-cell"><span class="issue-description">Issue Description:&nbsp;</span>{{$issue->content}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{$issues->links("pagination::bootstrap-4")}}
    </div>
</div>

<div id="solve-issue-modal" class="modal fade font-content" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="header-container">
                    <div class="modal-title custom-modal-title">Solve Issue</div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="solve-issue-form" novalidate class="needs-validation">
            <fieldset>
                <legend style="display:none;">Solve issue form</legend>
                {{ csrf_field() }}
                <div class="modal-body">                 
                    <div class="form-group">
                        <textarea required class="form-control" name="content" placeholder="Enter a reply message to send to the user ..." aria-label="Reply Message">{{ old('content') }}</textarea>
                        <div class="invalid-feedback">Please provide a reply to the user that submitted the issue</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn publish-button solve-issue">Solve</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </fieldset>
            </form>
        </div>
    </div>
</div>

@endsection