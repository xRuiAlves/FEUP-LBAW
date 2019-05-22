@extends('layouts.admin', ['activeTable' => 'categories'])

@section('table')

<div id="category-table" class="admin-dashboard col-12 col-md-10 col-xl-11">
    <div class="row no-gutters">
        <div class="col-12">
            <button class="btn action-btn">Remove selected categories</button>
            <button class="btn action-btn">Add category</button>
        </div>
    </div>
    <div class="content-table">
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th>Id</th>
                    <th>Category Name</th>
                    <th>Number of Events</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                <tr>
                    <td><input type="checkbox"></td>
                    <td>{{$category->id}}</td>
                    <td>{{$category->name}}</td>
                    <td>{{$category->n_events}}</td>
                    <td><button class="btn action-btn">Rename</button></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{$categories->links("pagination::bootstrap-4")}}
    </div>
</div>
@endsection