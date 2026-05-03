<?php

namespace App\Providers;

use App\Models\AppNotification;
use App\Models\Booking;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Inject unread notification count and pending bookings count into every view
        View::composer('*', function ($view) {
            $unread          = 0;
            $pendingBookings = 0;
            if (auth()->check()) {
                $unread = AppNotification::where('user_id', auth()->id())
                    ->where('is_read', false)
                    ->count();
                if (auth()->user()->isOrganizer()) {
                    $pendingBookings = Booking::where('status', 'pending')->count();
                }
            }
            $view->with('unreadNotifications', $unread);
            $view->with('pendingBookingsCount', $pendingBookings);
        });
    }
}
