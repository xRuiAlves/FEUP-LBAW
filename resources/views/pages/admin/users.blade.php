@extends('layouts.admin', ['activeTable' => 'users'])

@section('table')

<div id="user-table" class="admin-dashboard col-12 col-md-10 col-xl-11">
    <div class="collapse-title custom-title">Users</div>
    <div class="searchbar-container">
        <input class="searchbar" type="text" placeholder="User name, email, ..." />
        <i class="fas fa-search icon-right"></i>
    </div>
    <div class="row no-gutters">
        <div class="col-12">
            <button class="btn action-btn">Ban selected users</button>
            <button class="btn action-btn">Un-Ban selected users</button>
        </div>
    </div>
    <div class="content-table">
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td><input type="checkbox"></td>
                    <td>{{$user->name}}</td>
                    <td>{{$user->email}}</td>
                    <td>{{$user->is_disabled ? "Disabled" : "Active"}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{$users->links("pagination::bootstrap-4")}}
    </div>
</div>
@endsection