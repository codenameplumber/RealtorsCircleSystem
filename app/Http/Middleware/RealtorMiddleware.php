<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RealtorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Please sign in to access your dashboard.');
        }

        $user = auth()->user();

        if ($user->Status === 'pending') {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Your account is pending approval. You will be notified once approved.');
        }

        if (in_array($user->Status, ['suspended', 'inactive'])) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Your account has been suspended. Please contact support.');
        }

        return $next($request);
    }
}
