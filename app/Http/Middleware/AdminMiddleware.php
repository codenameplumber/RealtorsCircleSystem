<?php

namespace App\Http\Middleware;

use App\Models\AdminsTable;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('admin_id')) {
            return redirect()->route('admin.login')
                ->with('error', 'Admin access required. Please sign in.');
        }

        $admin = AdminsTable::find(session('admin_id'));

        if (! $admin || $admin->Status !== 'active') {
            session()->forget(['admin_id', 'admin_name', 'admin_role']);
            return redirect()->route('admin.login')
                ->with('error', 'Your account is no longer active. Please contact support.');
        }

        return $next($request);
    }
}
