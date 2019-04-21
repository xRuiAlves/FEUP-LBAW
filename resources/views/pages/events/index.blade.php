@if($event->is_disabled)
    @include('pages.events.disabled')
@elseif($event->is_cancelled)
    Event Cancelled! (WIP)
@else
    @include('pages.events.active')
@endif