<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
   public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $validator =  Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'preferred_categories' => 'nullable|string', 
        ]);
         if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user->update($request->only(['name', 'email', 'preferred_categories']));

        return response()->json(['message' => 'Profile updated successfully', 'user' => $user->fresh()]);
    }
}
