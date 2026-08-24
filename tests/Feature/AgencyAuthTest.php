<?php

namespace Tests\Feature;

use App\Livewire\Agency\Auth\Login;
use App\Models\AgencyPartner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AgencyAuthTest extends TestCase
{
    use RefreshDatabase;

    private function makeAgency(array $overrides = []): AgencyPartner
    {
        return AgencyPartner::create(array_merge([
            'company_name'  => 'Test Agency',
            'first_name'    => 'Test',
            'last_name'     => 'Owner',
            'email'         => 'agency@example.com',
            'password'      => Hash::make('password123'),
            'referral_code' => AgencyPartner::generateReferralCode(),
            'status'        => 'active',
        ], $overrides));
    }

    public function test_login_succeeds_for_active_agency(): void
    {
        $this->makeAgency();

        Livewire::test(Login::class)
            ->set('email', 'agency@example.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasNoErrors();

        $this->assertEquals(1, session('agency_id'));
    }

    public function test_login_rejected_for_suspended_agency(): void
    {
        $this->makeAgency(['status' => 'suspended']);

        Livewire::test(Login::class)
            ->set('email', 'agency@example.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasErrors('email');

        $this->assertNull(session('agency_id'));
    }

    public function test_login_rejected_for_pending_agency(): void
    {
        $this->makeAgency(['status' => 'pending']);

        Livewire::test(Login::class)
            ->set('email', 'agency@example.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasErrors('email');

        $this->assertNull(session('agency_id'));
    }

    public function test_dashboard_requires_login(): void
    {
        $this->get('/agency/dashboard')->assertRedirect(route('agency.login'));
    }

    public function test_middleware_kicks_out_session_when_agency_suspended_mid_session(): void
    {
        $agency = $this->makeAgency();

        $this->withSession(['agency_id' => $agency->id, 'agency_name' => $agency->company_name]);

        $agency->update(['status' => 'suspended']);

        $response = $this->get('/agency/dashboard');

        $response->assertRedirect(route('agency.login'));
    }

    public function test_middleware_allows_active_agency_through(): void
    {
        $agency = $this->makeAgency();

        $this->withSession(['agency_id' => $agency->id, 'agency_name' => $agency->company_name]);

        $response = $this->get('/agency/dashboard');

        $response->assertOk();
    }
}
