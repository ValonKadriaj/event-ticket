<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class AdminVenueController extends Controller
{   
    private array $validatorArray = [
        'name' => 'required|string|max:255',
        'location' => 'required|string|max:255',
        'capacity' => 'required|integer|min:1',
    ];
    public function index()
    {
        return response()->json(['venues' => Venue::all()]);
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
        $venue = Venue::create($request->only(array_keys($this->validatorArray)));
        return response()->json(['message' => 'Venue created successfully', 'venue' => $venue], 201);
    }

    public function show(Venue $venue)
    {
        if (!Gate::allows('admin')) {
            return response()->json(['message' => 'You are Unauthorized to make this request'], 401);
        }
        return response()->json(['venue' => $venue]);
    }

    public function update(Request $request, Venue $venue)
    {
         if (!Gate::allows('admin')) {
            return response()->json(['message' => 'You are Unauthorized to make this request'], 401);
        }
        $validator =  Validator::make($request->all(), $this->validatorArray);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $venue->update($request->only(array_keys($this->validatorArray)));
        return response()->json(['message' => 'Venue updated successfully', 'venue' => $venue]);
    }

    public function destroy(Venue $venue)
    {
        if (!Gate::allows('admin')) {
            return response()->json(['message' => 'You are Unauthorized to make this request'], 401);
        }
        if ($venue->events()->exists()) {
            return response()->json(['message' => 'Cannot delete venue with associated events'], 400);
        }
        $venue->delete();
        return response()->json(['message' => 'Venue deleted successfully']);
    }
}
