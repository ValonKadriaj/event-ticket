<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class AdminEventController extends Controller
{   
    private array $validatorArray = [
        'name' => 'required|string|max:255',
        'category' => 'required|string|max:255',
        'venue_id' => 'required|exists:venues,id',
        'start_time' => 'required|date|after:now',
        'end_time' => 'required|date|after:start_time',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
    ];
    public function index()
    {   
         if (!Gate::allows('admin')) {
            return response()->json(['message' => 'You are Unauthorized to make this request'], 401);
        }
        $events = Event::with('venue')->orderBy('start_time')->get();
        return response()->json(['events' => $events]);
    }


    public function store(Request $request)
    {   
         if (!Gate::allows('admin')) {
            return response()->json(['message' => 'You are Unauthorized to make this request'], 401);
        }
        $validator =  Validator::make($request->all(), $this->validatorArray);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $event = Event::create(array_merge($request->only(array_keys($this->validatorArray)), ['admin_id' => auth()->id()]));
        return response()->json(['message' => 'Event created successfully', 'event' => $event], 201);
    }


    public function show(Event $event)
    {   
         if (! Gate::allows('admin')) {
            return response()->json(['message' => 'You are Unauthorized to make this request'], 401);
        }
        $event->load('venue',  'tickets.user');
        return response()->json(['event' => $event]);
    }

    

   
    public function update(Request $request, Event $event)
    {   
      
         if (!Gate::allows('admin')) {
            return response()->json(['message' => 'You are Unauthorized to make this request'], 401);
        }
          
        $validator =  Validator::make($request->all(), $this->validatorArray);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $event->update($request->only(array_keys($this->validatorArray)));
        return response()->json(['message' => 'Event updated successfully', 'event' => $event]);
    }

 
    public function destroy(Event $event)
    {   
         if (!Gate::allows('admin')) {
            return response()->json(['message' => 'You are Unauthorized to make this request'], 401);
        }
        $event->delete();
        return response()->json(['message' => 'Event deleted successfully']);
    }
    
}