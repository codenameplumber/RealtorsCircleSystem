<?php

namespace Tests\Feature;

use App\Livewire\Admin\Agencies;
use App\Livewire\Admin\Agents;
use App\Livewire\Admin\Approvals;
use App\Livewire\Admin\CommissionSettings;
use App\Livewire\Admin\Properties;
use App\Mail\Agency\AgencyApprovedMail;
use App\Mail\Agency\AgencyWelcomeMail;
use App\Mail\Admin\WelcomeAgentMail;
use App\Mail\Agent\PropertyApprovedMail;
use App\Mail\Agent\PropertyRejectedMail;
use App\Mail\Agent\RealtorApprovedMail;
use App\Mail\Agent\RealtorRejectedMail;
use App\Models\AdminAuditLog;
use App\Models\AdminsTable;
use App\Models\AgencyPartner;
use App\Models\PropertiesTable;
use App\Models\UsersTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class AdminActionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = AdminsTable::create([
            'First_name' => 'Test',
            'Last_name'  => 'Admin',
            'Email'      => 'admin@example.com',
            'Password'   => Hash::make('password123'),
            'Role'       => 'manager',
            'Status'     => 'active',
        ]);

        session(['admin_id' => $admin->id, 'admin_name' => $admin->First_name, 'admin_role' => $admin->Role]);
    }

    private function makeRealtor(array $overrides = []): UsersTable
    {
        // api_token / email_verified_at are mass-assignment guarded on the
        // model; trusted test setup opts out rather than silently losing them.
        return UsersTable::unguarded(fn () => UsersTable::create(array_merge([
            'First_name'   => 'Pending',
            'Last_name'    => 'Realtor',
            'Email'        => 'pending-realtor@example.com',
            'Phone'        => '08012340000',
            'Password'     => Hash::make('password123'),
            'Status'       => 'pending',
            '2fa'          => 'disabled',
            'steps_signup' => 'complete',
            'api_token'    => hash('sha256', 'z'),
            'email_verified_at' => now(),
        ], $overrides)));
    }

    private function makeProperty(UsersTable $realtor, array $overrides = []): PropertiesTable
    {
        return PropertiesTable::create(array_merge([
            'user_id'         => $realtor->id,
            'title'           => 'Test Property',
            'type'            => 'Flat',
            'description'     => 'A nice place',
            'listing_type'    => 'sale',
            'street'          => '1 Test St',
            'neigborhood'     => 'Testville',
            'city'            => 'Lagos',
            'state'           => 'Lagos',
            'localarea'       => 'Ikeja',
            'country'         => 'Nigeria',
            'amenities'       => json_encode([]),
            'price'           => '1000000',
            'photos'          => json_encode([]),
            'videos'          => json_encode([]),
            'listing_status'  => 'active',
            'approval_status' => 'pending',
            'views'           => '0',
        ], $overrides));
    }

    public function test_approve_realtor_sends_email_and_logs_audit(): void
    {
        Mail::fake();
        $realtor = $this->makeRealtor();

        Livewire::test(Approvals::class)->call('approveRealtor', $realtor->id);

        $this->assertSame('active', $realtor->fresh()->Status);
        Mail::assertSent(RealtorApprovedMail::class);
        $this->assertDatabaseHas('admin_audit_logs', ['action' => 'realtor.approve', 'subject_id' => $realtor->id]);
    }

    public function test_reject_realtor_requires_reason_sends_email_and_logs_audit(): void
    {
        Mail::fake();
        $realtor = $this->makeRealtor();

        // Missing reason should fail validation
        Livewire::test(Approvals::class)
            ->call('openReject', $realtor->id, 'realtor')
            ->call('submitReject')
            ->assertHasErrors('rejectReason');

        Livewire::test(Approvals::class)
            ->call('openReject', $realtor->id, 'realtor')
            ->set('rejectReason', 'Documents did not match KYC requirements.')
            ->call('submitReject')
            ->assertHasNoErrors();

        $this->assertSame('suspended', $realtor->fresh()->Status);
        Mail::assertSent(RealtorRejectedMail::class);
        $this->assertDatabaseHas('admin_audit_logs', [
            'action'      => 'realtor.reject',
            'subject_id'  => $realtor->id,
            'description' => 'Documents did not match KYC requirements.',
        ]);
    }

    public function test_approve_property_sends_email_and_logs_audit(): void
    {
        Mail::fake();
        $realtor  = $this->makeRealtor();
        $property = $this->makeProperty($realtor);

        Livewire::test(Approvals::class)->call('approveProperty', $property->id);

        $this->assertSame('approved', $property->fresh()->approval_status);
        Mail::assertSent(PropertyApprovedMail::class);
        $this->assertDatabaseHas('admin_audit_logs', ['action' => 'property.approve', 'subject_id' => $property->id]);
    }

    public function test_reject_property_via_properties_page_sends_email_and_logs_audit(): void
    {
        Mail::fake();
        $realtor  = $this->makeRealtor();
        $property = $this->makeProperty($realtor);

        Livewire::test(Properties::class)
            ->call('openReject', $property->id)
            ->set('rejectReason', 'Photos do not match the listing description.')
            ->call('submitReject')
            ->assertHasNoErrors();

        $this->assertSame('rejected', $property->fresh()->approval_status);
        Mail::assertSent(PropertyRejectedMail::class);
        $this->assertDatabaseHas('admin_audit_logs', ['action' => 'property.reject', 'subject_id' => $property->id]);
    }

    public function test_viewing_realtor_exposes_kyc_documents_for_pending_approval(): void
    {
        $realtor = $this->makeRealtor([
            'kyc_front_url' => 'https://example.com/front.jpg',
            'kyc_back_url'  => 'https://example.com/back.jpg',
        ]);

        Livewire::test(Approvals::class)
            ->call('viewRealtor', $realtor->id)
            ->assertSet('detailOpen', true)
            ->assertSee('front.jpg')
            ->assertSee('back.jpg');
    }

    public function test_agency_approve_sends_email_and_logs_audit(): void
    {
        Mail::fake();
        $agency = AgencyPartner::create([
            'company_name'  => 'Pending Agency',
            'first_name'    => 'A',
            'last_name'     => 'B',
            'email'         => 'pending-agency@example.com',
            'password'      => Hash::make('password123'),
            'referral_code' => AgencyPartner::generateReferralCode(),
            'status'        => 'pending',
        ]);

        Livewire::test(Approvals::class)->call('approveAgency', $agency->id);

        $this->assertSame('active', $agency->fresh()->status);
        Mail::assertSent(AgencyApprovedMail::class);
        $this->assertDatabaseHas('admin_audit_logs', ['action' => 'agency.approve', 'subject_id' => $agency->id]);
    }

    public function test_create_agency_sends_welcome_email_and_logs_audit(): void
    {
        Mail::fake();

        Livewire::test(Agencies::class)
            ->call('openCreate')
            ->set('companyName', 'New Agency')
            ->set('firstName', 'Jane')
            ->set('lastName', 'Doe')
            ->set('email', 'newagency@example.com')
            ->set('phone', '08099998888')
            ->set('password', 'password123')
            ->set('state', 'Lagos')
            ->set('lga', 'Ikeja')
            ->call('create')
            ->assertHasNoErrors();

        $agency = AgencyPartner::where('email', 'newagency@example.com')->first();
        $this->assertNotNull($agency);
        Mail::assertSent(AgencyWelcomeMail::class);
        $this->assertDatabaseHas('admin_audit_logs', ['action' => 'agency.create', 'subject_id' => $agency->id]);
    }

    public function test_toggle_agency_status_logs_audit_with_correct_action(): void
    {
        Mail::fake();
        $agency = AgencyPartner::create([
            'company_name'  => 'Toggle Agency',
            'first_name'    => 'A',
            'last_name'     => 'B',
            'email'         => 'toggle-agency@example.com',
            'password'      => Hash::make('password123'),
            'referral_code' => AgencyPartner::generateReferralCode(),
            'status'        => 'active',
        ]);

        Livewire::test(Agencies::class)->call('toggleStatus', $agency->id);

        $this->assertSame('suspended', $agency->fresh()->status);
        $this->assertDatabaseHas('admin_audit_logs', ['action' => 'agency.suspend', 'subject_id' => $agency->id]);
    }

    public function test_create_agent_sends_welcome_email_and_logs_audit(): void
    {
        Mail::fake();

        Livewire::test(Agents::class)
            ->call('openAdd')
            ->set('first_name', 'New')
            ->set('last_name', 'Agent')
            ->set('email', 'newagent@example.com')
            ->set('password', 'password123')
            ->set('phone', '08077776666')
            ->set('state', 'Lagos')
            ->set('lga', 'Ikeja')
            ->set('specialization', 'Residential')
            ->call('addAgent');

        $agent = \App\Models\AgentsTable::where('Email', 'newagent@example.com')->first();
        $this->assertNotNull($agent);
        Mail::assertSent(WelcomeAgentMail::class);
        $this->assertDatabaseHas('admin_audit_logs', ['action' => 'agent.create', 'subject_id' => $agent->id]);
    }

    public function test_commission_settings_save_validates_totals_and_logs_audit(): void
    {
        Livewire::test(CommissionSettings::class)
            ->set('m1_listing_pct', 40)
            ->set('m1_referring_pct', 40)
            ->set('m1_agent_pct', 10)
            ->set('m1_platform_pct', 5) // totals 95, should fail
            ->call('save')
            ->assertHasErrors('m1_listing_pct');

        $this->assertDatabaseMissing('admin_audit_logs', ['action' => 'commission_settings.update']);

        Livewire::test(CommissionSettings::class)
            ->set('m1_listing_pct', 40)
            ->set('m1_referring_pct', 40)
            ->set('m1_agent_pct', 10)
            ->set('m1_platform_pct', 10)
            ->set('m2_listing_pct', 60)
            ->set('m2_agent_pct', 20)
            ->set('m2_platform_pct', 20)
            ->set('agency_override_pct', 20)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('admin_audit_logs', ['action' => 'commission_settings.update']);
    }

    public function test_audit_log_page_renders_recorded_entries(): void
    {
        AdminAuditLog::record('realtor.approve', 'UsersTable', 1, 'Test entry for audit log page');

        $response = $this->get('/admin/audit-log');

        $response->assertOk();
        $response->assertSee('Test entry for audit log page');
    }
}
