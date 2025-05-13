<?php

namespace App\Http\Controllers;

use App\Mail\TicketConfirmationEmail;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule as ValidationRule;

class TicketController extends Controller
{
     public function bookTicket(Request $request, Event $event)
    {
        $bookedSeatsCount = Ticket::where('event_id', $event->id)->count();
        if ($event->venue && $bookedSeatsCount >= $event->venue->capacity) {
            return response()->json(['message' => 'No available seats for this event'], 400);
        }

        $ticket = Ticket::create([
            'user_id' => auth()->id(),
            'event_id' => $event->id,
            'price' => $event->price ?? 10.00, 
            'seat_info' => $request->input('seat_info'),
            'booking_time' => now(),
        ]);
        Mail::to(auth()->user()->email)->send(new TicketConfirmationEmail(auth()->user(), $event, $ticket));
        return response()->json(['message' => 'Ticket booked successfully', 'ticket' => $ticket], 201);
    }
    public function getUserBookings(Request $request)
    {
       $bookings = auth()->user()->tickets()
        ->selectRaw('tickets.*, events.*')
        ->join('events', 'events.id', '=', 'tickets.event_id')
        ->orderByRaw('events.start_time >= NOW() DESC') 
        ->orderBy('events.start_time')                  
        ->get();

        return response()->json(['bookings' => $bookings]);
    }
}
