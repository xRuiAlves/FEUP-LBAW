@extends('layouts.admin', ['activeTable' => 'users'])

@section('table')

<div id="user-table" class="admin-dashboard col-12 col-md-10 col-xl-11">
    <div class="collapse-title custom-title">Users</div>
    <div class="searchbar-container">
        <form class="form-inline" action="" method="get">
            <label class="sr-only" for="inlineFormInputName2">Name</label>
            <input type="text" class="form-control mb-2 mr-sm-2" id="inlineFormInputName2" placeholder="Jane Doe" name="search">
            
            <button type="submit" class="btn btn-primary mb-2">Submit</button>
        </form>
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