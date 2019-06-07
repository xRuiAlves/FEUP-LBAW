@extends('layouts.admin', ['activeTable' => 'users'])

@section('asset_includes')
@parent
<script src="{{asset('js/admin_users_page.js')}}" defer></script>
@endsection

@section('table')

<div id="user-table" class="admin-dashboard col-12 col-md-10 col-xl-11">
    <div class="collapse-title custom-title">Users</div>
    <div class="searchbar-container">
        <form class="form-inline" method="get">
        <fieldset>
            <legend style="display:none;"> Search users form</legend>
            <label class="sr-only" for="inlineFormInputName2">Name</label>
            <input type="text" class="form-control mb-2 mr-sm-2" id="inlineFormInputName2" placeholder="Ex: Martha" name="search" aria-label="Search User">
            
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
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Account Type</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
            <tr class="user-entry" data-user-id="{{$user->id}}" data-user-name="{{$user->name}}" data-user-disabled="{{$user->is_disabled ? "true" : "false"}}">
                    <td>{{$user->name}}</td>
                    <td>{{$user->email}}</td>
                    <td>{{$user->is_admin ? "Admin" : "User"}}</td>
                    <td class="status">{{$user->is_disabled ? "Disabled" : "Active"}}</td>
                    <td class="actions">
                        <button 
                            class="btn action-btn account-enable-toggle" 
                            title="{{$user->is_disabled ? 'Enable user account' : 'Disable user account'}}">
                            <i class="{{$user->is_disabled ? 'fas fa-undo' : 'fas fa-ban'}}"></i>
                            <span class="text">{{$user->is_disabled ? 'Enable' : 'Disable'}}</span>
                        </button> 
                        <button 
                            {{$user->is_admin ? 'disabled' : ''}} 
                            class="btn action-btn promote-admin-button" 
                            title="{{$user->is_admin ? '' : 'Promote this user to admin'}}">
                            <i class="fas fa-user-tie"></i>
                            Promote
                        </button> 
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{$users->links("pagination::bootstrap-4")}}
    </div>
</div>

<div id="promote-to-admin-modal" class="modal fade font-content" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="header-container">
                    <div class="modal-title custom-modal-title">Promote user to Admin</div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="#">
            <fieldset>
                <legend style="display:none;">Admin promotion form</legend>
                <div class="modal-body">                 
                    Are you sure you want to promote this user to a platform administrator?
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn publish-button dismiss-notification">Promote</button>
                    <button type="button" class="btn btn-secondary close-button" data-dismiss="modal">Close</button>
                </div>
            </fieldset>
            </form>
        </div>
    </div>
</div>

<div id="user-status-modal" class="modal fade font-content" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="header-container">
                    <div class="modal-title custom-modal-title"></div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"> </div>
            <div class="modal-footer">
                <button type="button" class="btn publish-button action-1"></button>
                <button type="button" class="btn publish-button action-2"></button>
                <button type="button" class="btn btn-secondary close-button" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection