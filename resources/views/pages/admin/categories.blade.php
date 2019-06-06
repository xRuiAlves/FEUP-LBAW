@extends('layouts.admin', ['activeTable' => 'categories'])

@section('asset_includes')
@parent
<script src="{{asset('js/admin_categories_page.js')}}" defer></script>
@endsection

@section('table')

<div id="category-table" class="admin-dashboard col-12 col-md-10 col-xl-11">
    <div class="collapse-title custom-title">Event Categories</div>
    <div class="row no-gutters">
        <div class="col-12">
            <button class="btn action-btn" data-toggle="modal" data-target="#create-category-modal">Create new Event Category</button>
        </div>
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
                    <th>Category Name</th>
                    <th>Number of Events</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="categories-list">
                @foreach($categories as $category)
                <tr>
                    <td data-category-id={{$category->id}}>{{$category->id}}</td>
                    <td data-category-name={{$category->name}}>{{$category->name}}</td>
                    <td >{{$category->n_events}}</td>
                    <td><button class="btn action-btn rename-category-button">Rename</button></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{$categories->links("pagination::bootstrap-4")}}
    </div>
</div>

<div id="create-category-modal" class="modal fade font-content" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="header-container">
                    <div class="modal-title custom-modal-title">Create an Event Category</div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="create-category-form" novalidate class="needs-validation" action="#">
            <fieldset>
                <legend style="display:none;">Create category form</legend>
                {{ csrf_field() }}
                <div novalidate class="needs-validation">
                    <div class="modal-body"> 
                        <div class="form-group">
                            <input type="text" name="name" autocomplete="off" placeholder="Category name" required class="form-control" aria-label="Category Name">
                            <div class="invalid-feedback">Please provide a category name</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn publish-button create-category">Create</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </fieldset>
            </form>
        </div>
    </div>
</div>

<div id="rename-category-modal" class="modal fade font-content" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="header-container">
                    <div class="modal-title custom-modal-title">Rename Category</div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="rename-category-form" novalidate class="needs-validation" action="#">
            <fieldset>
                <legend style="display:none;">Rename category form</legend>
                {{ csrf_field() }}
                <div novalidate class="needs-validation">
                        <div class="modal-body"> 
                            <div class="form-group">
                                <input type="text" name="name" autocomplete="off" placeholder="New category name" required class="form-control" aria-label="New Category Name">
                                <div class="invalid-feedback">Please provide the new category name</div>
                            </div>
                        </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn publish-button rename-category">Rename</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </fieldset>
            </form>
        </div>
    </div>
</div>

@endsection