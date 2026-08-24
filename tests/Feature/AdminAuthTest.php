<?php

namespace Tests\Feature;

use App\Models\AdminsTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(array $overrides = []): AdminsTable
    {
        return AdminsTable::create(array_merge([
            'First_name' => 'Test',
            'Last_name'  => 'Admin',
            'Email'      => 'admin@example.com',
            'Password'   => Hash::make('password123'),
            'Role'       => 'manager',
            'Status'     => 'active',
        ], $overrides));
    }

    public function test_login_succeeds_for_active_admin(): void
    {
        $this->makeAdmin();

        $response = $this->post('/admin/login', [
            'email'    => 'admin@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertEquals(1, session('admin_id'));
    }

    public function test_login_rejected_for_wrong_password(): void
    {
        $this->makeAdmin();

        $response = $this->post('/admin/login', [
            'email'    => 'admin@example.com',
            'password' => 'wrong',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertNull(session('admin_id'));
    }

    public function test_dashboard_requires_login(): void
    {
        $this->get('/admin/dashboard')->assertRedirect(route('admin.login'));
    }

    public function test_middleware_kicks_out_session_when_admin_deactivated_mid_session(): void
    {
        $admin = $this->makeAdmin();

        $this->withSession(['admin_id' => $admin->id, 'admin_name' => $admin->First_name, 'admin_role' => $admin->Role]);

        $admin->update(['Status' => 'inactive']);

        // Uses /admin/audit-log rather than /admin/dashboard because the dashboard's
        // Overview widget runs a MySQL-only MONTH() query that SQLite (this test's
        // driver) can't execute — unrelated to the middleware behavior under test.
        $response = $this->get('/admin/audit-log');

        $response->assertRedirect(route('admin.login'));
    }

    public function test_middleware_allows_active_admin_through(): void
    {
        $admin = $this->makeAdmin();

        $this->withSession(['admin_id' => $admin->id, 'admin_name' => $admin->First_name, 'admin_role' => $admin->Role]);

        $response = $this->get('/admin/audit-log');

        $response->assertOk();
    }
}
