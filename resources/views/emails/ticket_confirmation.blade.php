@component('mail::message')
# Your Ticket Booking Confirmation

Dear {{ $user->name }},

Thank you for booking ticket(s) for the following event:

**Event:** {{ $event->name }}
**Venue:** {{ $event->venue->name }} ({{ $event->venue->location }})
**Date:** {{ $event->start_time }}

**Booking Details:**
- Ticket ID: {{ $ticket->id }}
@if ($ticket->seat_info)
, Seat: {{ $ticket->seat_info }}
@endif

Thank you for your purchase!

@slot('subcopy')
@component('mail::subcopy')
If you have any questions, please contact our support team.
@endcomponent
@endslot

@endcomponent