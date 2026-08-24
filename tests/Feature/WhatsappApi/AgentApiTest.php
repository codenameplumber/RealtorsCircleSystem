<?php

namespace Tests\Feature\WhatsappApi;

use App\Mail\Agent\PropertyApprovedMail;
use App\Mail\Agent\RealtorApprovedMail;
use App\Models\AgentsTable;
use App\Models\PropertiesTable;
use App\Models\UsersTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AgentApiTest extends TestCase
{
    use RefreshDatabase;

    private array $headers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->headers = ['X-API-Key' => config('services.whatsapp_api.key')];
    }

    private function makeAgent(array $overrides = []): AgentsTable
    {
        return AgentsTable::create(array_merge([
            'First_name' => 'Wa', 'Last_name' => 'Agent', 'Email' => 'wa.agent@example.com',
            'Password' => Hash::make('x'), 'Phone' => '08099990000',
            'Assigned_area' => 'Ikeja', 'Status' => 'active', 'api_token' => hash('sha256', uniqid('', true)),
        ], $overrides));
    }

    private function makeRealtorInArea(string $lga = 'Ikeja', array $overrides = []): UsersTable
    {
        // api_token / email_verified_at are mass-assignment guarded on the
        // model; trusted test setup opts out rather than silently losing them.
        return UsersTable::unguarded(fn () => UsersTable::create(array_merge([
            'First_name' => 'Area', 'Last_name' => 'Realtor', 'Email' => 'area.realtor@example.com',
            'Phone' => '08010101010', 'Password' => Hash::make('x'), 'Status' => 'pending',
            '2fa' => 'disabled', 'steps_signup' => 'complete', 'api_token' => hash('sha256', uniqid('', true)),
            'Lga' => $lga, 'kyc_status' => 'pending',
        ], $overrides)));
    }

    public function test_no_api_key_rejected(): void
    {
        $this->postJson('/api/wa/v1/agent/identify', ['phone' => '08099990000'])->assertStatus(401);
    }

    public function test_identify_returns_assigned_area(): void
    {
        $this->makeAgent();

        $this->withHeaders($this->headers)->postJson('/api/wa/v1/agent/identify', ['phone' => '08099990000'])
            ->assertOk()
            ->assertJsonPath('data.assigned_area', 'Ikeja');
    }

    public function test_inactive_agent_blocked_from_actions(): void
    {
        $this->makeAgent(['Status' => 'inactive']);

        $this->withHeaders($this->headers)->getJson('/api/wa/v1/agent/realtors/pending?phone=08099990000')
            ->assertStatus(403);
    }

    public function test_pending_realtors_scoped_to_agent_lga_only(): void
    {
        $this->makeAgent();
        $this->makeRealtorInArea('Ikeja');
        $this->makeRealtorInArea('Yaba', ['Phone' => '08020202020', 'Email' => 'other-lga@example.com']);

        $response = $this->withHeaders($this->headers)->getJson('/api/wa/v1/agent/realtors/pending?phone=08099990000');

        $response->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_approve_realtor_sends_email_and_activates(): void
    {
        Mail::fake();
        $this->makeAgent();
        $realtor = $this->makeRealtorInArea();

        $this->withHeaders($this->headers)->postJson("/api/wa/v1/agent/realtors/{$realtor->id}/approve", ['phone' => '08099990000'])
            ->assertOk();

        $this->assertSame('active', $realtor->fresh()->Status);
        Mail::assertSent(RealtorApprovedMail::class);
    }

    public function test_approve_realtor_outside_lga_returns_404(): void
    {
        $this->makeAgent();
        $realtor = $this->makeRealtorInArea('Yaba', ['Phone' => '08020202020', 'Email' => 'diff@example.com']);

        $this->withHeaders($this->headers)->postJson("/api/wa/v1/agent/realtors/{$realtor->id}/approve", ['phone' => '08099990000'])
            ->assertStatus(404);

        $this->assertSame('pending', $realtor->fresh()->Status);
    }

    public function test_reject_realtor_requires_reason(): void
    {
        $this->makeAgent();
        $realtor = $this->makeRealtorInArea();

        $this->withHeaders($this->headers)->postJson("/api/wa/v1/agent/realtors/{$realtor->id}/reject", ['phone' => '08099990000'])
            ->assertStatus(422);
    }

    public function test_approve_property_sends_email(): void
    {
        Mail::fake();
        $this->makeAgent();
        $realtor  = $this->makeRealtorInArea('Ikeja', ['Status' => 'active', 'email_verified_at' => now()]);
        $property = PropertiesTable::create([
            'user_id' => $realtor->id, 'title' => 'Agent Test Property', 'type' => 'Flat',
            'description' => 'd', 'listing_type' => 'sale', 'street' => 's', 'neigborhood' => 'n',
            'city' => 'Lagos', 'state' => 'Lagos', 'localarea' => 'Ikeja', 'country' => 'Nigeria',
            'amenities' => json_encode([]), 'price' => '1000000', 'photos' => json_encode([]),
            'videos' => json_encode([]), 'listing_status' => 'active', 'approval_status' => 'pending', 'views' => '0',
        ]);

        $this->withHeaders($this->headers)->postJson("/api/wa/v1/agent/properties/{$property->id}/approve", ['phone' => '08099990000'])
            ->assertOk();

        $this->assertSame('approved', $property->fresh()->approval_status);
        Mail::assertSent(PropertyApprovedMail::class);
    }
}
