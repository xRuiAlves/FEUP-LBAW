<div id="user-table" data-event_id="{{$event->id}}" class="admin-dashboard col-12 col-md-10 col-xl-11">
    <div class="collapse-title custom-title">Users</div>
    <div class="searchbar-container">
        <form class="form-inline" action="" method="get">
            <label class="sr-only" for="inlineFormInputName2">Name</label>
        <input type="text" class="form-control mb-2 mr-sm-2" id="inlineFormInputName2" placeholder="Ex: Martha" name="search" value="{{$searchQuery}}">
            
            <button type="submit" class="btn btn-primary mb-2">Search</button>
        </form>
    </div>
    <div class="content-table">
        <table class="table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{$user->name}}</td>
                    <td>{{$user->email}}</td>
                    <td class="text-right"><button class="btn action" data-user_id="{{$user->id}}">{{$action}}</button>
                </tr>
                @endforeach

                @if ($users->isEmpty())
                    <tr><td>
                        No Results found
                    </td></tr>
                @endif
            </tbody>
        </table>
        {{$users->links("pagination::bootstrap-4")}}
    </div>
</div>