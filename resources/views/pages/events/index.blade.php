@if($event->is_disabled)
    Event Disabled! (WIP)
@elseif($event->is_cancelled)
    Event Cancelled! (WIP)
@else
    @include('pages.events.active')
@endif