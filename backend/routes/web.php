<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\{AuthController, SocialAuthController};
use App\Http\Controllers\{
    VillaController,
    AmenityController,
    ReservationController,
    PaymentController,
    ReviewController,
    MessageController,
    DisputeController,
    AvailabilityController,
};
use App\Http\Controllers\Admin\{
    AdminDashboardController,
    AdminUserController,
    AdminDisputeController,
};

Route::get('/', fn() => redirect()->away(env('FRONTEND_URL', 'http://127.0.0.1:5174')));

// ============================================================
// PUBLIC ROUTES
// ============================================================
Route::prefix('auth')->group(function () {
    Route::post('/register',         [AuthController::class, 'register']);
    Route::post('/login',            [AuthController::class, 'login']);
    Route::post('/forgot-password',  [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password',   [AuthController::class, 'resetPassword']);
    Route::get('/{provider}/redirect', [SocialAuthController::class, 'redirect']);
    Route::get('/{provider}/callback', [SocialAuthController::class, 'callback']);
});

// Public villa listing & detail
Route::get('/villas',                          [VillaController::class, 'index']);
Route::get('/villas/{villa}',                  [VillaController::class, 'show']);
Route::get('/villas/{villa}/availability',     [AvailabilityController::class, 'show']);
Route::get('/villas/{villa}/reviews',          [ReviewController::class, 'villaReviews']);
Route::get('/reservations/quote',              [ReservationController::class, 'quote']);

// Stripe webhook (no auth)
Route::post('/payments/webhook', [PaymentController::class, 'webhook']);

// ============================================================
// AUTHENTICATED ROUTES
// ============================================================
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('/logout',          [AuthController::class, 'logout']);
        Route::get('/me',               [AuthController::class, 'me']);
        Route::put('/profile',          [AuthController::class, 'updateProfile']);
        Route::put('/password',         [AuthController::class, 'updatePassword']);
    });

    // Notifications
    Route::get('/notifications',        fn(\Illuminate\Http\Request $r) => $r->user()->notifications()->paginate(15));
    Route::post('/notifications/read-all', fn(\Illuminate\Http\Request $r) => $r->user()->unreadNotifications->markAsRead() ?? response()->json(['message' => 'ok']));

    // Messages (all roles)
    Route::get('/messages/conversations',  [MessageController::class, 'index']);
    Route::get('/messages/conversation',   [MessageController::class, 'conversation']);
    Route::post('/messages',               [MessageController::class, 'store']);
    Route::patch('/messages/{message}/read', [MessageController::class, 'markRead']);
    Route::get('/messages/unread-count',   [MessageController::class, 'unreadCount']);

    // --------------------------------------------------------
    // TRAVELER
    // --------------------------------------------------------
    Route::middleware('role:traveler')->group(function () {
        Route::post('/reservations',              [ReservationController::class, 'store']);
        Route::get('/my-reservations',            [ReservationController::class, 'myReservations']);
        Route::get('/reservations/{reservation}', [ReservationController::class, 'show']);
        Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel']);

        Route::post('/payments/intent',    [PaymentController::class, 'createIntent']);
        Route::post('/payments/confirm',   [PaymentController::class, 'confirm']);
        Route::get('/my-payments',         [PaymentController::class, 'myPayments']);

        Route::post('/reviews',            [ReviewController::class, 'store']);
    });

    // --------------------------------------------------------
    // OWNER
    // --------------------------------------------------------
    Route::middleware('role:owner')->group(function () {
        Route::get('/my-villas',                            [VillaController::class, 'myVillas']);
        Route::post('/villas',                              [VillaController::class, 'store']);
        Route::put('/villas/{villa}',                       [VillaController::class, 'update']);
        Route::delete('/villas/{villa}',                    [VillaController::class, 'destroy']);
        Route::post('/villas/{villa}/photos',               [VillaController::class, 'uploadPhoto']);
        Route::delete('/villas/{villa}/photos/{photo}',     [VillaController::class, 'deletePhoto']);

        Route::put('/villas/{villa}/availability',          [AvailabilityController::class, 'update']);
        Route::post('/villas/{villa}/availability/block',   [AvailabilityController::class, 'blockRange']);

        Route::get('/villa-reservations',                   [ReservationController::class, 'villaReservations']);
        Route::post('/reservations/{reservation}/approve',  [ReservationController::class, 'approve']);
        Route::post('/reservations/{reservation}/reject',   [ReservationController::class, 'reject']);

        Route::post('/reviews/{review}/reply',              [ReviewController::class, 'reply']);
        Route::get('/earnings',                             [PaymentController::class, 'ownerEarnings']);

        Route::get('/my-amenities',                         [AmenityController::class, 'index']);
        Route::post('/amenities',                           [AmenityController::class, 'store']);
        Route::put('/amenities/{amenity}',                  [AmenityController::class, 'update']);
        Route::delete('/amenities/{amenity}',               [AmenityController::class, 'destroy']);

        Route::get('/my-disputes',                          [DisputeController::class, 'myDisputes']);
        Route::post('/disputes',                            [DisputeController::class, 'store']);
        Route::put('/disputes/{dispute}',                   [DisputeController::class, 'update']);
        Route::delete('/disputes/{dispute}',                [DisputeController::class, 'destroy']);
    });

    // --------------------------------------------------------
    // OWNER + TRAVELER shared (reservation show/cancel)
    // --------------------------------------------------------
    Route::middleware('role:owner,traveler')->group(function () {
        Route::get('/reservations/{reservation}',           [ReservationController::class, 'show']);
    });

    // --------------------------------------------------------
    // ADMIN
    // --------------------------------------------------------
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard',                         [AdminDashboardController::class, 'index']);

        Route::get('/users',                             [AdminUserController::class, 'index']);
        Route::get('/users/{user}',                      [AdminUserController::class, 'show']);
        Route::post('/users/{user}/suspend',             [AdminUserController::class, 'suspend']);
        Route::post('/users/{user}/activate',            [AdminUserController::class, 'activate']);
        Route::delete('/users/{user}',                   [AdminUserController::class, 'destroy']);

        Route::get('/villas',                            [VillaController::class, 'index']);
        Route::post('/villas/{villa}/approve',           [VillaController::class, 'adminApprove']);
        Route::post('/villas/{villa}/reject',            [VillaController::class, 'adminReject']);
        Route::delete('/villas/{villa}',                 [VillaController::class, 'destroy']);

        Route::get('/disputes',                          [AdminDisputeController::class, 'index']);
        Route::get('/disputes/{dispute}',                [AdminDisputeController::class, 'show']);
        Route::post('/disputes/{dispute}/resolve',       [AdminDisputeController::class, 'resolve']);

        Route::post('/reviews/{review}/moderate',        [ReviewController::class, 'moderate']);

        Route::get('/reservations',                      fn() => \App\Models\Reservation::with(['villa:id,title','traveler:id,name'])->latest()->paginate(20));
        Route::get('/payments',                          fn() => \App\Models\Payment::with(['reservation.villa:id,title'])->latest()->paginate(20));
    });
});