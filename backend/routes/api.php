<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    VillaController,
    Client\ProfileController,
    Auth\AuthController,
    ReservationController,
    Client\ReclamationController,
    Admin\ReservationController as AdminReservationController,
    Admin\ReclamationController as AdminReclamationController,
};

Route::prefix('client')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::post('/reservations', [ReservationController::class, 'store']);
        Route::get('/reservations', [ReservationController::class, 'index']);
        Route::post('/reclamations', [ReclamationController::class, 'store']);
        Route::get('/reclamations', [ReclamationController::class, 'index']);
    });
});

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::patch('/reservations/{reservation}/status', [AdminReservationController::class, 'updateStatus'])->name('reservations.updateStatus');
    Route::patch('/reclamations/{reclamation}/respond', [AdminReclamationController::class, 'respond'])->name('reclamations.respond');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('role:owner')->group(function () {
        Route::get('/my-villas', [VillaController::class, 'myVillas']);
        Route::post('/villas', [VillaController::class, 'store']);
        Route::put('/villas/{villa}', [VillaController::class, 'update']);
        Route::delete('/villas/{villa}', [VillaController::class, 'destroy']);
        Route::post('/villas/{villa}/photos', [VillaController::class, 'uploadPhoto']);
        Route::delete('/villas/{villa}/photos/{photo}', [VillaController::class, 'deletePhoto']);
    });
});
