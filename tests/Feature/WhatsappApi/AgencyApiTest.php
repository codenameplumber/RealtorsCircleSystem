<?php

namespace Tests\Feature\WhatsappApi;

use App\Models\AgencyPartner;
use App\Models\CommissionTable;
use App\Models\PropertiesTable;
use App\Models\UsersTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AgencyApiTest extends TestCase
{
    use RefreshDatabase;

    private array $headers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->headers = ['X-API-Key' => config('services.whatsapp_api.key')];
    }

    private function makeAgency(array $overrides = []): AgencyPartner
    {
        return AgencyPartner::create(array_merge([
            'company_name' => 'Wa Agency', 'first_name' => 'A', 'last_name' => 'B',
            'email' => 'wa.agency@example.com', 'phone' => '08088880000',
            'password' => Hash::make('x'), 'referral_code' => AgencyPartner::generateReferralCode(),
            'status' => 'active',
        ], $overrides));
    }

    public function test_no_api_key_rejected(): void
    {
        $this->postJson('/api/wa/v1/agency/identify', ['phone' => '08088880000'])->assertStatus(401);
    }

    public function test_identify_returns_agency_profile(): void
    {
        $this->makeAgency();

        $this->withHeaders($this->headers)->postJson('/api/wa/v1/agency/identify', ['phone' => '08088880000'])
            ->assertOk()
            ->assertJsonPath('data.company_name', 'Wa Agency');
    }

    public function test_suspended_agency_blocked_from_reports(): void
    {
        $this->makeAgency(['status' => 'suspended']);

        $this->withHeaders($this->headers)->getJson('/api/wa/v1/agency/network?phone=08088880000')
            ->assertStatus(403);
    }

    public function test_network_counts_only_linked_realtors(): void
    {
        $agency = $this->makeAgency();
        // api_token is mass-assignment guarded; trusted test setup opts out.
        UsersTable::unguarded(function () use ($agency) {
            UsersTable::create([
                'First_name' => 'Net', 'Last_name' => 'Realtor', 'Email' => 'net.realtor@example.com',
                'Phone' => '08010009999', 'Password' => Hash::make('x'), 'Status' => 'active',
                '2fa' => 'disabled', 'steps_signup' => 'complete', 'api_token' => hash('sha256', 'x'),
                'agency_id' => $agency->id,
            ]);
            UsersTable::create([
                'First_name' => 'Unlinked', 'Last_name' => 'Realtor', 'Email' => 'unlinked@example.com',
                'Phone' => '08010008888', 'Password' => Hash::make('x'), 'Status' => 'active',
                '2fa' => 'disabled', 'steps_signup' => 'complete', 'api_token' => hash('sha256', 'y'),
            ]);
        });

        $response = $this->withHeaders($this->headers)->getJson('/api/wa/v1/agency/network?phone=08088880000');

        $response->assertOk()->assertJsonPath('data.total_realtors', 1);
    }

    public function test_earnings_sums_agency_override_amount(): void
    {
        $agency  = $this->makeAgency();
        $realtor = UsersTable::unguarded(fn () => UsersTable::create([
            'First_name' => 'Earn', 'Last_name' => 'Realtor', 'Email' => 'earn.realtor@example.com',
            'Phone' => '08010007777', 'Password' => Hash::make('x'), 'Status' => 'active',
            '2fa' => 'disabled', 'steps_signup' => 'complete', 'api_token' => hash('sha256', 'z'),
            'agency_id' => $agency->id,
        ]));
        $property = PropertiesTable::create([
            'user_id' => $realtor->id, 'title' => 'Agency Deal Property', 'type' => 'Flat',
            'description' => 'd', 'listing_type' => 'sale', 'street' => 's', 'neigborhood' => 'n',
            'city' => 'Lagos', 'state' => 'Lagos', 'localarea' => 'Ikeja', 'country' => 'Nigeria',
            'amenities' => [], 'price' => '5000000', 'photos' => [],
            'videos' => [], 'listing_status' => 'active', 'approval_status' => 'approved', 'views' => '0',
        ]);
        CommissionTable::create([
            'property_id' => $property->id, 'listing_realtor_id' => $realtor->id,
            'agency_id' => $agency->id, 'total_amount' => 5000000,
            'listing_realtor_amount' => 2000000, 'agency_override_amount' => 100000, 'status' => 'paid',
        ]);

        $response = $this->withHeaders($this->headers)->getJson('/api/wa/v1/agency/earnings?phone=08088880000');

        $response->assertOk();
        $this->assertEquals(100000, $response->json('data.total_earned'));
    }
}
