@section('asset_includes')
@parent
<script src="{{asset('js/events.js')}}" defer></script>
@endsection

@if($event->is_disabled)
    @include('pages.events.disabled')
@elseif($event->is_cancelled)
    @include('pages.events.cancelled')
@else
    @include('pages.events.active')
@endif