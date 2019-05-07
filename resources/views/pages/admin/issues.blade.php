@extends('layouts.admin', ['activeTable' => 'issues'])

@section('table')

<div id="issue-table" class="admin-dashboard col-12 col-md-10 col-xl-11">
    <div class="custom-title">Issues</div>
    <div class="searchbar-container">
        <form class="form-inline" action="" method="get">
            <label class="sr-only" for="inlineFormInputName2">Name</label>
            <input type="text" class="form-control mb-2 mr-sm-2" id="inlineFormInputName2" placeholder="Jane Doe" name="search">
            
            <button type="submit" class="btn btn-primary mb-2">Submit</button>
        </form>
    </div>
    <div class="row no-gutters">
        <div class="col-12">
            <button class="btn action-btn">Close selected issues</button>
            <button class="btn float-right">Show closed issues</button>
        </div>
    </div>
    <div class="content-table">
        <table class="table">
            <thead>
                <tr>
                    <th></th>
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
                <tr class="issue-header" data-toggle="collapse" data-target="#issue{{$issue->id}}collapse">
                    <td><input type="checkbox"/></td>
                    <td>{{$issue->id}}</td>
                    <td>{{$issue->creator_name}}</td>
                    <td>{{$issue->date}}</td>
                    <td>{{$issue->time}}</td>
                    <td>{{$issue->title}}</td>
                    <td>@if($issue->is_solved) Solved @else <button class="btn action-btn">Close</button> @endif</td>
                </tr>
                <tr class="collapse" id="issue{{$issue->id}}collapse">
                    <td></td>
                    <td class="text-center" colspan="5"><i class="fas fa-chevron-down"></i><br><p class="text-left">{{$issue->content}}</p></td>
                    <td></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{$issues->links("pagination::bootstrap-4")}}
    </div>
</div>
@endsection