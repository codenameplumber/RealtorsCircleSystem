<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Mail\Agency\AgencyPasswordResetMail;
use App\Models\AgencyPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AgencyDashboardController extends Controller
{
    private function agency(): ?AgencyPartner
    {
        return AgencyPartner::find(session('agency_id'));
    }

    private function isLoggedIn(): bool
    {
        return session()->has('agency_id');
    }

    // ── Authentication ────────────────────────────────────────────────────────
    // Login submission is handled by App\Livewire\Agency\Auth\Login; this GET
    // route only renders the page shell that embeds it.

    public function showLogin()
    {
        if ($this->isLoggedIn()) {
            return redirect()->route('agency.dashboard');
        }

        return view('dashboard.agency.login');
    }

    public function logout()
    {
        session()->forget(['agency_id', 'agency_name']);
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('agency.login');
    }

    // ── Password Reset ────────────────────────────────────────────────────────

    public function showForgotPassword()
    {
        if ($this->isLoggedIn()) {
            return redirect()->route('agency.dashboard');
        }

        return view('dashboard.agency.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        try {
            $agency = AgencyPartner::where('email', $request->email)->first();

            if ($agency && \App\Support\EmailThrottle::allows($agency->email, 'password-reset')) {
                $token    = Str::random(64);
                $agency->forceFill([
                    'password_reset_token'      => hash('sha256', $token),
                    'password_reset_expires_at' => now()->addHour(),
                ])->save();

                $resetUrl = route('agency.reset-password.show', [
                    'token' => $token,
                    'email' => encrypt($agency->email),
                ]);

                Mail::to($agency->email)->send(new AgencyPasswordResetMail($agency, $resetUrl));
            }

            return back()->with('success', 'If an account with that email exists, a reset link has been sent.');
        } catch (\Throwable $e) {
            Log::error('Agency reset link error: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Failed to send reset link. Please try again.']);
        }
    }

    public function showResetPassword(Request $request, string $token)
    {
        if ($this->isLoggedIn()) {
            return redirect()->route('agency.dashboard');
        }

        try {
            $email = decrypt($request->query('email', ''));
        } catch (\Throwable $e) {
            abort(403, 'Invalid or tampered password reset link.');
        }

        $agency = AgencyPartner::where('email', $email)->first();

        if (! $agency
            || ! $agency->password_reset_token
            || ! $agency->password_reset_expires_at
            || ! hash_equals($agency->password_reset_token, hash('sha256', $token))
            || now()->isAfter($agency->password_reset_expires_at)) {
            return redirect()->route('agency.forgot-password')
                ->withErrors(['email' => 'This reset link is invalid or has expired.']);
        }

        return view('dashboard.agency.reset-password', compact('token', 'email'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'                 => 'required',
            'email'                 => 'required|email',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        try {
            $agency = AgencyPartner::where('email', $request->email)->first();

            if (! $agency
                || ! $agency->password_reset_token
                || ! $agency->password_reset_expires_at
                || ! hash_equals($agency->password_reset_token, hash('sha256', $request->token))
                || now()->isAfter($agency->password_reset_expires_at)) {
                return back()->withErrors(['email' => 'This reset link is invalid or has expired.']);
            }

            $agency->forceFill([
                'password'                  => Hash::make($request->password),
                'password_reset_token'      => null,
                'password_reset_expires_at' => null,
            ])->save();

            return redirect()->route('agency.login')
                ->with('success', 'Password reset successfully. You can now sign in.');
        } catch (\Throwable $e) {
            Log::error('Agency password reset error: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Failed to reset password. Please try again.']);
        }
    }

    // ── Dashboard Pages ───────────────────────────────────────────────────────

    public function index()       { return view('dashboard.agency.index'); }
    public function network()     { return view('dashboard.agency.network'); }
    public function performance() { return view('dashboard.agency.performance'); }
    public function earnings()    { return view('dashboard.agency.earnings'); }
    public function payments()    { return view('dashboard.agency.payments'); }
    public function settings()    { return view('dashboard.agency.settings'); }
}
