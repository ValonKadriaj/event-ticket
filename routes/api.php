<?php

use App\Http\Controllers\AdminEventController;
use App\Http\Controllers\AdminVenueController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CustomSanctumAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json(['user' =>$request->user()]);
    });
    Route::get('/bookings', [TicketController::class, 'getUserBookings']);
    Route::put('/profile', [UserController::class, 'updateProfile']);

    Route::prefix('events')->group(function () {
        Route::get('/', [EventController::class, 'index']);
        Route::get('/search', [EventController::class, 'search']);
        Route::middleware('throttle:3,1')->post('/{event}/book', [TicketController::class, 'bookTicket']);
        Route::post('/', [AdminEventController::class, 'store']);
        Route::get('/create', [AdminEventController::class, 'create']); 
        Route::get('/{event}', [AdminEventController::class, 'show']);
        Route::put('/{event}', [AdminEventController::class, 'update']);
        Route::delete('/{event}', [AdminEventController::class, 'destroy']);
        Route::get('/{event}/edit', [AdminEventController::class, 'edit']);
    });
    Route::prefix('venues')->group(function () {
        Route::get('/', [AdminVenueController::class, 'index']);
        Route::post('/', [AdminVenueController::class, 'store']);
        Route::get('/{venue}', [AdminVenueController::class, 'show']);
        Route::put('/{venue}', [AdminVenueController::class, 'update']);
        Route::delete('/{venue}', [AdminVenueController::class, 'destroy']);
    });


  
  

});

Route::post('/login', [AuthController::class, 'login']);