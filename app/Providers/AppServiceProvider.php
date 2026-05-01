<?php

namespace App\Providers;

use App\Models\AppNotification;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Inject unread notification count into every view
        View::composer('*', function ($view) {
            $count = 0;
            if (auth()->check()) {
                $count = AppNotification::where('user_id', auth()->id())
                    ->where('is_read', false)
                    ->count();
            }
            $view->with('unreadNotifications', $count);
        });
    }
}
