<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\Dashboard\RealtorDashboardController;
use App\Http\Controllers\Web\Dashboard\AdminDashboardController;
use App\Http\Controllers\Web\Dashboard\AgentDashboardController;
use App\Http\Controllers\Web\Dashboard\AgencyDashboardController;
use App\Http\Controllers\Web\PaymentLinkController;
use App\Http\Controllers\Web\RealtorRatingController;
use App\Http\Controllers\Web\WhatsappUploadController;

// Legacy routes
Route::get('/coming-soon', fn() => view('home.home'));
Route::get('/waitlist', fn() => view('home/waitlist'));

// Public pages
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/properties', [PageController::class, 'properties'])->name('properties');
Route::get('/properties/{hash}', [PageController::class, 'propertyDetail'])->name('property.detail');
Route::get('/realtors', [PageController::class, 'realtors'])->name('realtors');
Route::get('/realtors/{hash}', [PageController::class, 'realtorDetail'])->name('realtor.detail');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');

// Rating API (public) — deduplicated per visitor and rate limited; see
// RealtorRatingController for why a session id alone is not enough.
Route::post('/api/rate-realtor', [RealtorRatingController::class, 'store'])
    ->middleware('throttle:public-form')
    ->name('realtor.rate');

Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');

// Payment-link stub (preview only — no real gateway integration)
Route::get('/pay/{token}',  [PaymentLinkController::class, 'show'])->name('payment.show');
Route::post('/pay/{token}', [PaymentLinkController::class, 'process'])->middleware('throttle:public-form')->name('payment.process');

// Auth pages (web sessions — guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', fn() => view('auth.login'))->name('login');
    Route::get('/register', fn() => view('auth.register'))->name('register');
    Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('password.request');
    Route::get('/reset-password/{token}', function (string $token) {
        try {
            $email = decrypt(request()->query('email', ''));
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(403, 'Invalid or tampered password reset link.');
        }
        return view('auth.reset-password', ['token' => $token, 'email' => $email]);
    })->name('password.reset');

    // Handle password reset form submission
    Route::post('/reset-password', function (Request $request) {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // Use the capitalized 'Password' column matching the users_tables schema
                $user->forceFill(['Password' => Hash::make($password)])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Password reset successfully! You can now sign in.')
            : back()->withErrors(['email' => [__($status)]]);
    })->middleware('throttle:password-reset')->name('password.update');
});

// Email verification (signed link sent on registration)
Route::get('/email/verify/{id}/{hash}', function (Request $request, int $id) {
    $user = \App\Models\UsersTable::find($id);

    if (! $user) {
        abort(404);
    }

    if (! hash_equals(sha1($user->Email), (string) $request->route('hash'))) {
        abort(403, 'Invalid or tampered verification link.');
    }

    if (! $user->email_verified_at) {
        $user->forceFill(['email_verified_at' => now()])->save();
    }

    return redirect()->route('login')->with('success', 'Email verified successfully! You can now sign in.');
})->middleware(['signed', 'throttle:api'])->name('verification.verify');

// WhatsApp-triggered signed upload links (KYC docs, property photos) — human
// browser pages reached from a link sent over WhatsApp; the signature itself
// is the credential, so these carry no other auth.
Route::middleware(['signed', 'throttle:uploads'])->group(function () {
    Route::get('/wa/kyc/{id}', [WhatsappUploadController::class, 'showKyc'])->name('whatsapp.kyc.show');
    Route::post('/wa/kyc/{id}', [WhatsappUploadController::class, 'submitKyc']);

    Route::get('/wa/property-photos/{id}', [WhatsappUploadController::class, 'showPropertyPhotos'])->name('whatsapp.property-photos.show');
    Route::post('/wa/property-photos/{id}', [WhatsappUploadController::class, 'submitPropertyPhotos']);
});

// Logout
Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

// ========================================================
// REALTOR DASHBOARD (requires auth + active status)
// ========================================================
Route::prefix('realtor')->name('realtor.')->middleware(['auth', 'realtor'])->group(function () {
    Route::get('/dashboard',       [RealtorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/properties',             [RealtorDashboardController::class, 'properties'])->name('properties');
    Route::get('/properties/add',         fn() => view('dashboard.realtor.property-form'))->name('properties.create');
    Route::get('/properties/{hash}',       [RealtorDashboardController::class, 'showProperty'])->name('properties.show');
    Route::get('/properties/{hash}/edit', [RealtorDashboardController::class, 'editProperty'])->name('properties.edit');
    Route::get('/leads',           [RealtorDashboardController::class, 'leads'])->name('leads');
    Route::get('/appointments',    [RealtorDashboardController::class, 'appointments'])->name('appointments');
    Route::get('/commission',      [RealtorDashboardController::class, 'commission'])->name('commission');
    Route::get('/payments',        [RealtorDashboardController::class, 'payments'])->name('payments');
    Route::get('/settings',        [RealtorDashboardController::class, 'settings'])->name('settings');

});

// ========================================================
// ADMIN DASHBOARD (separate session-based auth)
// ========================================================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login',  [AdminDashboardController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminDashboardController::class, 'login'])->middleware('throttle:login')->name('login.submit');
    Route::post('/logout',[AdminDashboardController::class, 'logout'])->name('logout');

    // Password reset (public)
    Route::get('/forgot-password',         [AdminDashboardController::class, 'showForgotPassword'])->name('forgot-password');
    Route::post('/forgot-password',        [AdminDashboardController::class, 'sendResetLink'])->middleware('throttle:password-reset')->name('forgot-password.send');
    Route::get('/reset-password/{token}',  [AdminDashboardController::class, 'showResetPassword'])->name('reset-password.show');
    Route::post('/reset-password',         [AdminDashboardController::class, 'resetPassword'])->middleware('throttle:public-form')->name('reset-password.update');

    Route::middleware('admin')->group(function () {
        Route::get('/dashboard',              [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/realtors',               [AdminDashboardController::class, 'realtors'])->name('realtors');
        Route::get('/approvals',              [AdminDashboardController::class, 'approvals'])->name('approvals');
        Route::post('/realtors/{id}/approve', [AdminDashboardController::class, 'approveRealtor'])->name('realtor.approve');
        Route::get('/properties',             [AdminDashboardController::class, 'properties'])->name('properties');
        Route::post('/properties/{id}/approve',[AdminDashboardController::class, 'approveProperty'])->name('property.approve');
        Route::get('/agents',                 [AdminDashboardController::class, 'agents'])->name('agents');
        Route::get('/agencies',              [AdminDashboardController::class, 'agencies'])->name('agencies');
        Route::get('/commission',             [AdminDashboardController::class, 'commission'])->name('commission');
        Route::get('/payments',               [AdminDashboardController::class, 'payments'])->name('payments');
        Route::get('/reports',                [AdminDashboardController::class, 'reports'])->name('reports');
        Route::get('/audit-log',              [AdminDashboardController::class, 'auditLog'])->name('audit-log');
        Route::get('/settings',               [AdminDashboardController::class, 'settings'])->name('settings');
        Route::post('/settings/profile',      [AdminDashboardController::class, 'updateProfile'])->name('settings.profile');
        Route::post('/settings/password',     [AdminDashboardController::class, 'updatePassword'])->name('settings.password');
    });
});

// ========================================================
// AGENT DASHBOARD (separate session-based auth)
// ========================================================
Route::prefix('agent')->name('agent.')->group(function () {
    // Public (guest-only) agent auth routes — login/forgot/reset are handled
    // entirely by Livewire components (App\Livewire\Agent\Auth\*); these GET
    // routes only render the page shell that embeds them.
    Route::get('/login',  [AgentDashboardController::class, 'showLogin'])->name('login');
    Route::post('/logout',[AgentDashboardController::class, 'logout'])->name('logout');

    // Password reset (no registration)
    Route::get('/forgot-password',         [AgentDashboardController::class, 'showForgotPassword'])->name('forgot-password');
    Route::get('/reset-password/{token}',  [AgentDashboardController::class, 'showResetPassword'])->name('reset-password.show');

    // Protected pages (Livewire-powered)
    Route::middleware('agent')->group(function () {
        Route::get('/dashboard',     [AgentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/realtors',      [AgentDashboardController::class, 'realtors'])->name('realtors');
        Route::get('/properties',    [AgentDashboardController::class, 'properties'])->name('properties');
        Route::get('/appointments',  [AgentDashboardController::class, 'appointments'])->name('appointments');
        Route::get('/clients',       [AgentDashboardController::class, 'clients'])->name('clients');
        Route::get('/commission',    [AgentDashboardController::class, 'commission'])->name('commission');
        Route::get('/payments',      [AgentDashboardController::class, 'payments'])->name('payments');
        Route::get('/reports',       [AgentDashboardController::class, 'reports'])->name('reports');
        Route::get('/settings',      [AgentDashboardController::class, 'settings'])->name('settings');
    });
});

// ========================================================
// AGENCY PARTNER DASHBOARD (separate session-based auth)
// ========================================================
Route::prefix('agency')->name('agency.')->group(function () {
    // Login is handled by Livewire (App\Livewire\Agency\Auth\Login); this GET
    // route only renders the page shell that embeds it.
    Route::get('/login',  [AgencyDashboardController::class, 'showLogin'])->name('login');
    Route::post('/logout',[AgencyDashboardController::class, 'logout'])->name('logout');

    Route::get('/forgot-password',        [AgencyDashboardController::class, 'showForgotPassword'])->name('forgot-password');
    Route::post('/forgot-password',       [AgencyDashboardController::class, 'sendResetLink'])->middleware('throttle:password-reset')->name('forgot-password.send');
    Route::get('/reset-password/{token}', [AgencyDashboardController::class, 'showResetPassword'])->name('reset-password.show');
    Route::post('/reset-password',        [AgencyDashboardController::class, 'resetPassword'])->middleware('throttle:public-form')->name('reset-password.update');

    Route::middleware('agency')->group(function () {
        Route::get('/dashboard',    [AgencyDashboardController::class, 'index'])->name('dashboard');
        Route::get('/network',      [AgencyDashboardController::class, 'network'])->name('network');
        Route::get('/performance',  [AgencyDashboardController::class, 'performance'])->name('performance');
        Route::get('/earnings',     [AgencyDashboardController::class, 'earnings'])->name('earnings');
        Route::get('/payments',     [AgencyDashboardController::class, 'payments'])->name('payments');
        Route::get('/settings',     [AgencyDashboardController::class, 'settings'])->name('settings');
    });
});
