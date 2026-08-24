<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();

        $this->configureRateLimiters();
        $this->throttleLivewireUpdates();

        View::composer('dashboard.admin._sidebar', function ($view) {
            if (session('admin_id')) {
                $pending = \App\Models\UsersTable::where('Status', 'pending')->count()
                         + \App\Models\PropertiesTable::where('approval_status', 'pending')->count();
                $view->with('pendingCount', $pending);
            }
        });
    }

    /**
     * Named rate limiters used by the `throttle:<name>` route middleware.
     *
     * Credential-testing surfaces are keyed by email *and* IP so one attacker
     * cannot lock a victim out of their own account by burning the limit on
     * their email alone — the email component only narrows an IP's budget.
     * Livewire-driven forms (all four portal logins) cannot use route
     * middleware, since every Livewire action POSTs to the same
     * /livewire/update endpoint; those throttle in-component via
     * App\Livewire\Concerns\ThrottlesAttempts against these same conventions.
     */
    protected function configureRateLimiters(): void
    {
        // Non-Livewire credential submission (admin login posts a real form).
        RateLimiter::for('login', fn (Request $request) => [
            Limit::perMinute(5)->by($this->emailAndIp($request)),
            Limit::perMinute(20)->by($request->ip()),
        ]);

        // Anything that sends mail to an address the caller names. Deliberately
        // strict: this is the email-bombing surface.
        RateLimiter::for('password-reset', fn (Request $request) => [
            Limit::perHour(3)->by($this->emailAndIp($request)),
            Limit::perHour(10)->by($request->ip()),
        ]);

        RateLimiter::for('register', fn (Request $request) => Limit::perHour(5)->by($request->ip()));

        // Public, unauthenticated JSON API.
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(30)->by($request->ip()));

        // Authenticated JSON API — keyed to the token holder when we have one.
        RateLimiter::for('api-auth', fn (Request $request) => Limit::perMinute(60)
            ->by($request->user()?->getAuthIdentifier() ?: $request->ip()));

        // n8n-facing WhatsApp surface. A single trusted caller, so the ceiling
        // is high enough never to bite in normal operation but still bounds a
        // leaked X-API-Key.
        RateLimiter::for('whatsapp-api', fn (Request $request) => Limit::perMinute(120)->by($request->ip()));

        // Public forms that write rows: contact, booking, ratings.
        RateLimiter::for('public-form', fn (Request $request) => Limit::perHour(15)->by($request->ip()));

        // Signed upload links reached from WhatsApp — bounds file-storage abuse
        // if a link is forwarded or leaks.
        RateLimiter::for('uploads', fn (Request $request) => Limit::perHour(20)->by($request->ip()));

        // Global ceiling on the shared Livewire endpoint. Deliberately
        // generous: a `wire:model.live` search box fires one request per
        // debounced keystroke, so a real user can legitimately produce dozens a
        // minute. This is a backstop against hammering an expensive component,
        // not a substitute for the per-action limits in
        // App\Livewire\Concerns\ThrottlesAttempts.
        RateLimiter::for('livewire', fn (Request $request) => Limit::perMinute(300)->by($request->ip()));
    }

    /**
     * Every Livewire action POSTs to this one route, so it is the only place a
     * route-level limit can reach Livewire traffic at all. Without it the
     * endpoint is completely unbounded.
     */
    protected function throttleLivewireUpdates(): void
    {
        Livewire::setUpdateRoute(fn ($handle) => Route::post('/livewire/update', $handle)
            ->middleware(['web', 'throttle:livewire'])
            ->name('livewire.update'));
    }

    /**
     * Build a throttle key from the submitted email plus the client IP.
     */
    protected function emailAndIp(Request $request): string
    {
        $email = Str::lower((string) $request->input('email', $request->input('Email', '')));

        return Str::transliterate($email) . '|' . $request->ip();
    }
}
