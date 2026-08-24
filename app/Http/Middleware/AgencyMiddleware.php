<?php

namespace App\Http\Middleware;

use App\Models\AgencyPartner;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AgencyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('agency_id')) {
            return redirect()->route('agency.login')
                ->with('error', 'Agency Partner access required. Please sign in.');
        }

        $agency = AgencyPartner::find(session('agency_id'));

        if (! $agency || $agency->status !== 'active') {
            session()->forget(['agency_id', 'agency_name']);
            return redirect()->route('agency.login')
                ->with('error', 'Your agency account is no longer active. Please contact support.');
        }

        return $next($request);
    }
}
