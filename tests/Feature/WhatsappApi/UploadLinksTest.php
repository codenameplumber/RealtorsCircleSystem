<?php

namespace Tests\Feature\WhatsappApi;

use App\Models\PropertiesTable;
use App\Models\UsersTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class UploadLinksTest extends TestCase
{
    use RefreshDatabase;

    private function makeRealtor(array $overrides = []): UsersTable
    {
        // api_token / email_verified_at are mass-assignment guarded on the
        // model; trusted test setup opts out rather than silently losing them.
        return UsersTable::unguarded(fn () => UsersTable::create(array_merge([
            'First_name' => 'Link', 'Last_name' => 'Realtor', 'Email' => 'link.realtor@example.com',
            'Phone' => '08066660000', 'Password' => Hash::make('x'), 'Status' => 'pending',
            '2fa' => 'disabled', 'steps_signup' => 'complete', 'api_token' => hash('sha256', uniqid('', true)),
        ], $overrides)));
    }

    private function makeProperty(UsersTable $realtor, array $overrides = []): PropertiesTable
    {
        return PropertiesTable::create(array_merge([
            'user_id' => $realtor->id, 'title' => 'Link Test Property', 'type' => 'Flat',
            'description' => 'd', 'listing_type' => 'sale', 'street' => 's', 'neigborhood' => 'n',
            'city' => 'Lagos', 'state' => 'Lagos', 'localarea' => 'Ikeja', 'country' => 'Nigeria',
            'amenities' => [], 'price' => '1000000', 'photos' => [],
            'videos' => [], 'listing_status' => 'active', 'approval_status' => 'pending', 'views' => '0',
        ], $overrides));
    }

    public function test_kyc_form_rejects_unsigned_url(): void
    {
        $realtor = $this->makeRealtor();

        $this->get("/wa/kyc/{$realtor->id}")->assertForbidden();
    }

    public function test_kyc_form_loads_with_valid_signature(): void
    {
        $realtor = $this->makeRealtor();
        $url     = URL::temporarySignedRoute('whatsapp.kyc.show', now()->addHours(1), ['id' => $realtor->id]);

        $this->get($url)->assertOk()->assertSee($realtor->First_name);
    }

    public function test_kyc_submission_stores_documents_and_sets_pending_status(): void
    {
        Storage::fake('kycaws');
        $realtor = $this->makeRealtor();
        $url     = URL::temporarySignedRoute('whatsapp.kyc.show', now()->addHours(1), ['id' => $realtor->id]);

        $response = $this->post($url, [
            'kyc_id_type' => 'National ID (NIN)',
            'kyc_front'   => UploadedFile::fake()->image('front.jpg'),
            'kyc_back'    => UploadedFile::fake()->image('back.jpg'),
        ]);

        $response->assertOk();
        $realtor->refresh();
        $this->assertSame('pending', $realtor->kyc_status);
        $this->assertSame('National ID (NIN)', $realtor->kyc_id_type);
        $this->assertNotNull($realtor->kyc_front_url);
        $this->assertNotNull($realtor->kyc_back_url);
    }

    public function test_kyc_submission_requires_front_image(): void
    {
        Storage::fake('kycaws');
        $realtor = $this->makeRealtor();
        $url     = URL::temporarySignedRoute('whatsapp.kyc.show', now()->addHours(1), ['id' => $realtor->id]);

        $this->from($url)->post($url, ['kyc_id_type' => 'National ID (NIN)'])
            ->assertSessionHasErrors('kyc_front');
    }

    public function test_property_photos_form_rejects_unsigned_url(): void
    {
        $realtor  = $this->makeRealtor();
        $property = $this->makeProperty($realtor);

        $this->get("/wa/property-photos/{$property->id}")->assertForbidden();
    }

    public function test_property_photos_submission_appends_to_existing_photos(): void
    {
        Storage::fake('photoaws');
        $realtor  = $this->makeRealtor();
        $property = $this->makeProperty($realtor, ['photos' => ['https://example.com/existing.jpg']]);
        $url      = URL::temporarySignedRoute('whatsapp.property-photos.show', now()->addHours(1), ['id' => $property->id]);

        $response = $this->post($url, [
            'photos' => [UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.jpg')],
        ]);

        $response->assertOk();
        $property->refresh();
        $this->assertCount(3, $property->photos);
    }
}
