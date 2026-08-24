<?php

namespace Tests\Feature;

use App\Livewire\Agent\Auth\Login;
use App\Models\AgentsTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AgentAuthTest extends TestCase
{
    use RefreshDatabase;

    private function makeAgent(array $overrides = []): AgentsTable
    {
        return AgentsTable::create(array_merge([
            'First_name'    => 'Test',
            'Last_name'     => 'Agent',
            'Email'         => 'agent@example.com',
            'Password'      => Hash::make('password123'),
            'Phone'         => '08010000001',
            'Assigned_area' => 'Ikeja',
            'Status'        => 'active',
            'api_token'     => hash('sha256', 'y'),
        ], $overrides));
    }

    public function test_login_succeeds_for_active_agent(): void
    {
        $this->makeAgent();

        Livewire::test(Login::class)
            ->set('email', 'agent@example.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasNoErrors();

        $this->assertEquals(1, session('agent_id'));
    }

    public function test_login_rejected_for_wrong_password(): void
    {
        $this->makeAgent();

        Livewire::test(Login::class)
            ->set('email', 'agent@example.com')
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors('email');

        $this->assertNull(session('agent_id'));
    }

    public function test_login_rejected_for_inactive_agent(): void
    {
        $this->makeAgent(['Status' => 'inactive']);

        Livewire::test(Login::class)
            ->set('email', 'agent@example.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasErrors('email');

        $this->assertNull(session('agent_id'));
    }

    public function test_dashboard_requires_login(): void
    {
        $this->get('/agent/dashboard')->assertRedirect(route('agent.login'));
    }

    public function test_middleware_kicks_out_session_when_agent_deactivated_mid_session(): void
    {
        $agent = $this->makeAgent();

        // Simulate an existing logged-in session
        $this->withSession(['agent_id' => $agent->id, 'agent_name' => $agent->First_name]);

        // Deactivate the agent after the session was established
        $agent->update(['Status' => 'inactive']);

        $response = $this->get('/agent/dashboard');

        $response->assertRedirect(route('agent.login'));
    }

    public function test_middleware_allows_active_agent_through(): void
    {
        $agent = $this->makeAgent();

        $this->withSession(['agent_id' => $agent->id, 'agent_name' => $agent->First_name]);

        $response = $this->get('/agent/dashboard');

        $response->assertOk();
    }
}
