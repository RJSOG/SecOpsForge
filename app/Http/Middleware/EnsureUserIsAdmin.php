<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * Must run after the 'auth' middleware. Blocks any authenticated user
     * who isn't flagged as admin (currently: the single account created by
     * AdminSeeder) from reaching note-editing routes.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->is_admin) {
            abort(403, 'Réservé à l\'administrateur.');
        }

        return $next($request);
    }
}
