<?php

namespace App\Http\Middleware;

use App\Models\AgentsTable;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AgentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('agent_id')) {
            return redirect()->route('agent.login')
                ->with('error', 'Agent access required. Please sign in.');
        }

        $agent = AgentsTable::find(session('agent_id'));

        if (! $agent || $agent->Status !== 'active') {
            session()->forget(['agent_id', 'agent_name']);
            return redirect()->route('agent.login')
                ->with('error', 'Your account is no longer active. Please contact support.');
        }

        return $next($request);
    }
}
