<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AgentsTable;
use Illuminate\Http\Request;

class AgentDashboardController extends Controller
{
    private function agent(): ?AgentsTable
    {
        return AgentsTable::find(session('agent_id'));
    }

    private function isLoggedIn(): bool
    {
        return session()->has('agent_id');
    }

    // ── Authentication ────────────────────────────────────────────────────────
    // Login, forgot-password, and reset-password submission are all handled by
    // App\Livewire\Agent\Auth\{Login,ForgotPassword,ResetPassword}. The methods
    // below only render the page shell that embeds each component.

    public function showLogin()
    {
        if ($this->isLoggedIn()) {
            return redirect()->route('agent.dashboard');
        }

        return view('dashboard.agent.login');
    }

    public function logout()
    {
        session()->forget(['agent_id', 'agent_name']);
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('agent.login');
    }

    // ── Password Reset ────────────────────────────────────────────────────────

    public function showForgotPassword()
    {
        if ($this->isLoggedIn()) {
            return redirect()->route('agent.dashboard');
        }

        return view('dashboard.agent.forgot-password');
    }

    public function showResetPassword(Request $request, string $token)
    {
        if ($this->isLoggedIn()) {
            return redirect()->route('agent.dashboard');
        }

        try {
            $email = decrypt($request->query('email', ''));
        } catch (\Throwable $e) {
            abort(403, 'Invalid or tampered password reset link.');
        }

        $agent = AgentsTable::where('Email', $email)->first();

        if (! $agent || ! $agent->isValidResetToken($token)) {
            return redirect()->route('agent.forgot-password')
                ->withErrors(['email' => 'This reset link is invalid or has expired.']);
        }

        return view('dashboard.agent.reset-password', compact('token', 'email'));
    }

    // ── Dashboard Pages (render Livewire wrappers) ────────────────────────────

    public function index()
    {
        return view('dashboard.agent.index');
    }

    public function realtors()
    {
        return view('dashboard.agent.realtors');
    }

    public function properties()
    {
        return view('dashboard.agent.properties');
    }

    public function appointments()
    {
        return view('dashboard.agent.appointments');
    }

    public function clients()
    {
        return view('dashboard.agent.clients');
    }

    public function commission()
    {
        return view('dashboard.agent.commission');
    }

    public function payments()
    {
        return view('dashboard.agent.payments');
    }

    public function reports()
    {
        return view('dashboard.agent.reports');
    }

    public function settings()
    {
        return view('dashboard.agent.settings');
    }
}
