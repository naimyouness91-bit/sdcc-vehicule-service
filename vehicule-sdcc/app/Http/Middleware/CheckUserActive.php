<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    /**
     * Handle an incoming request.
     *
     * Check if the authenticated user's account is disabled.
     * If disabled, log them out and redirect to login with a message.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Skip check if user not authenticated
        if (!$user) {
            return $next($request);
        }

        // Check if user account is disabled
        if ($user->isInactive()) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // If JSON request, return JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Votre compte a été désactivé. Contactez votre administrateur.',
                    'status' => 'disabled',
                ], 403);
            }

            // Redirect to login with warning message
            return redirect('/login')->with('warning', 'Votre compte a été désactivé. Contactez votre administrateur.');
        }

        return $next($request);
    }
}
