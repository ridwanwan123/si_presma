<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        /*
        |--------------------------------------------------------------------------
        | CHECK ROLE
        |--------------------------------------------------------------------------
        */

        if (!in_array($user->role->nama, $roles)) {

            abort(403, 'Kamu tidak punya akses ke halaman ini.');
        }

        return $next($request);
    }
}