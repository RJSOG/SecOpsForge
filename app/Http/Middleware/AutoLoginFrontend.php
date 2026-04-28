<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AutoLoginFrontend
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            $user = User::where('email', config('services.front.email'))->first();

            if ($user && Hash::check(config('services.front.secret'), $user->password)) {
                Auth::login($user);
            }
        }

        return $next($request);
    }
}
