<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Mail\Admin\AdminPasswordResetMail;
use App\Models\AdminsTable;
use App\Models\CommissionTable;
use App\Models\PropertiesTable;
use App\Models\UsersTable;
use App\Models\PropertyInquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminDashboardController extends Controller
{
    public function index()        { return view('dashboard.admin.index'); }
    public function realtors()     { return view('dashboard.admin.realtors'); }
    public function agents()       { return view('dashboard.admin.agents'); }
    public function agencies()     { return view('dashboard.admin.agencies'); }
    public function approvals()    { return view('dashboard.admin.approvals'); }
    public function properties()   { return view('dashboard.admin.properties'); }
    public function commission()   { return view('dashboard.admin.commission'); }
    public function payments()     { return view('dashboard.admin.payments'); }
    public function reports()      { return view('dashboard.admin.reports'); }
    public function auditLog()     { return view('dashboard.admin.audit-log'); }

    public function approveRealtor(Request $request, $id)
    {
        UsersTable::findOrFail($id)->update(['Status' => $request->action === 'approve' ? 'active' : 'suspended']);
        return back()->with('success', 'Realtor status updated.');
    }

    public function approveProperty(Request $request, $id)
    {
        PropertiesTable::findOrFail($id)->update(['approval_status' => $request->action === 'approve' ? 'approved' : 'rejected']);
        return back()->with('success', 'Property status updated.');
    }

    public function settings()
    {
        $admin = AdminsTable::findOrFail(session('admin_id'));
        return view('dashboard.admin.settings', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $admin = AdminsTable::findOrFail(session('admin_id'));

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:admins_tables,Email,' . $admin->id,
            'phone'      => 'nullable|string|max:20',
        ]);

        $admin->update([
            'First_name' => $request->first_name,
            'Last_name'  => $request->last_name,
            'Email'      => $request->email,
            'Phone'      => $request->phone,
        ]);

        session(['admin_name' => $request->first_name]);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $admin = AdminsTable::findOrFail(session('admin_id'));

        $request->validate([
            'current_password'      => 'required',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        if (! Hash::check($request->current_password, $admin->Password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withFragment('password');
        }

        $admin->update(['Password' => Hash::make($request->password)]);

        return back()->with('success', 'Password changed successfully.')->withFragment('password');
    }

    // Admin login
    public function showLogin()
    {
        if (session()->has('admin_id')) return redirect()->route('admin.dashboard');
        return view('dashboard.admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $admin = AdminsTable::where('Email', $request->email)->first();

        if (! $admin || ! Hash::check($request->password, $admin->Password)) {
            return back()->withErrors(['email' => 'Invalid credentials.']);
        }

        // Rotate the session id on privilege change so a pre-login id planted
        // via a cookie cannot survive into the authenticated admin session.
        $request->session()->regenerate();

        session(['admin_id' => $admin->id, 'admin_name' => $admin->First_name, 'admin_role' => $admin->Role]);

        return redirect()->route('admin.dashboard');
    }

    public function logout()
    {
        session()->forget(['admin_id', 'admin_name', 'admin_role']);

        // Invalidate rather than just forgetting the keys, so nothing else the
        // admin session accumulated survives the logout.
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    // Admin forgot / reset password
    public function showForgotPassword()
    {
        if (session()->has('admin_id')) return redirect()->route('admin.dashboard');
        return view('dashboard.admin.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        try {
            $admin = AdminsTable::where('Email', $request->email)->first();

            if ($admin && \App\Support\EmailThrottle::allows($admin->Email, 'password-reset')) {
                $token    = $admin->generateResetToken();
                $resetUrl = route('admin.reset-password.show', [
                    'token' => $token,
                    'email' => encrypt($admin->Email),
                ]);

                Mail::to($admin->Email)->send(new AdminPasswordResetMail($admin, $resetUrl));
            }

            return back()->with('success', 'If an account with that email exists, a reset link has been sent.');
        } catch (\Throwable $e) {
            Log::error('Admin reset link error: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Failed to send reset link. Please try again.']);
        }
    }

    public function showResetPassword(Request $request, string $token)
    {
        if (session()->has('admin_id')) return redirect()->route('admin.dashboard');

        try {
            $email = decrypt($request->query('email', ''));
        } catch (\Throwable $e) {
            abort(403, 'Invalid or tampered password reset link.');
        }

        $admin = AdminsTable::where('Email', $email)->first();

        if (! $admin || ! $admin->isValidResetToken($token)) {
            return redirect()->route('admin.forgot-password')
                ->withErrors(['email' => 'This reset link is invalid or has expired.']);
        }

        return view('dashboard.admin.reset-password', compact('token', 'email'));
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
            $admin = AdminsTable::where('Email', $request->email)->first();

            if (! $admin || ! $admin->isValidResetToken($request->token)) {
                return back()->withErrors(['email' => 'This reset link is invalid or has expired.']);
            }

            $admin->update(['Password' => Hash::make($request->password)]);
            $admin->clearResetToken();

            return redirect()->route('admin.login')
                ->with('success', 'Password reset successfully. You can now sign in.');
        } catch (\Throwable $e) {
            Log::error('Admin password reset error: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Failed to reset password. Please try again.']);
        }
    }
}
