<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Organizer\EventController as OrganizerEventController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

// ─── Public Routes ────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

// Booking (public — login required to submit)
Route::get('/booking', [BookingController::class, 'create'])->name('booking.create');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store')->middleware('auth');
Route::get('/admin/bookings', [BookingController::class, 'index'])->name('admin.bookings.index')->middleware(['auth', 'admin']);

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

Route::get('/map', [MapController::class, 'index'])->name('map.index');
Route::get('/map/events', [MapController::class, 'events'])->name('map.events'); // JSON for Leaflet

// ─── Authentication Routes ────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

// ─── Authenticated User Routes ────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [UserController::class, 'edit'])->name('user.profile');
    Route::put('/profile', [UserController::class, 'update'])->name('user.profile.update');
    Route::put('/profile/password', [UserController::class, 'updatePassword'])->name('user.password.update');

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{event}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
});

// ─── Organizer Routes ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'organizer'])->prefix('organizer')->name('organizer.')->group(function () {
    Route::get('/dashboard', [OrganizerEventController::class, 'dashboard'])->name('dashboard');
    Route::get('/events', [OrganizerEventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [OrganizerEventController::class, 'create'])->name('events.create');
    Route::post('/events', [OrganizerEventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}/edit', [OrganizerEventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [OrganizerEventController::class, 'update'])->name('events.update');
    Route::patch('/events/{event}/archive', [OrganizerEventController::class, 'archive'])->name('events.archive');
    Route::patch('/events/{event}/unarchive', [OrganizerEventController::class, 'unarchive'])->name('events.unarchive');
    Route::delete('/events/{event}', [OrganizerEventController::class, 'destroy'])->name('events.destroy');
});

// ─── Admin Routes ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Event management
    Route::get('/events', [AdminEventController::class, 'index'])->name('events.index');
    Route::post('/events/{event}/approve', [AdminEventController::class, 'approve'])->name('events.approve');
    Route::post('/events/{event}/reject', [AdminEventController::class, 'reject'])->name('events.reject');
    Route::post('/events/{event}/feature', [AdminEventController::class, 'feature'])->name('events.feature');
    Route::delete('/events/{event}', [AdminEventController::class, 'destroy'])->name('events.destroy');

    // User management
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::put('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.role');
});

