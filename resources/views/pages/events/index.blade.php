@if($event->status === 'Active')
    @include('pages.events.active')
@elseif($event->status === 'Disabled')
    Event Disabled! (WIP)
@elseif($event->status === 'Cancelled')
    Event Cancelled! (WIP)
@else
    <h1>Event in impossible state!</h1>
@endif