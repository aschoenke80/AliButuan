<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OrganizerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Allow organizers and admins
        if (!auth()->check() || !in_array(auth()->user()->role, ['organizer', 'admin'])) {
            abort(403, 'Access denied. Organizers only.');
        }

        return $next($request);
    }
}
