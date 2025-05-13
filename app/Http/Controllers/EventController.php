<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
        public function index(Request $request)
        {   
            $user = auth()->user();
            $query = Event::with('venue')
                ->where('start_time', '>', now())
                ->orderBy('start_time');

            if ($request->has('category')) {
                $query->where('category', $request->input('category'));
            }

            if ($request->has('venue')) {
                $query->whereHas('venue', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->input('venue') . '%');
                });
            }
            if($user->preferred_categories != null){
                $query->whereIn('category', explode(',', $user->preferred_categories));
            }
            
            if ($request->has('date_from') && $request->has('date_to')) {
                $query->whereBetween('start_time', [$request->input('date_from'), $request->input('date_to')]);
            } elseif ($request->has('date_from')) {
                $query->where('start_time', '>=', $request->input('date_from'));
            } elseif ($request->has('date_to')) {
                $query->where('start_time', '<=', $request->input('date_to'));
            }
            return response()->json(['events' => $query->get()]);
        }
        public function search(Request $request)
        {
            $query = $request->input('query');

            if (!$query) {
                return response()->json(['message' => 'Please provide a search query'], 400);
            }

            $events = Event::with('venue')
                ->where('name', 'like', '%' . $query . '%')
                ->orWhereHas('venue', function ($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%');
                    $q->orWhere('location', 'like', '%' . $query . '%'); 
                })
                ->orderBy('start_time')
                ->get(); 

            return response()->json(['results' => $events]);
        }

}
