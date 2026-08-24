<?php

namespace App\Http\Controllers\Api\Whatsapp;

use App\Helpers\PhoneNormalizer;
use App\Http\Controllers\Controller;
use App\Mail\Agent\PropertyApprovedMail;
use App\Mail\Users\EmailVerificationMail;
use App\Mail\WelcomeRealtor;
use App\Models\AgencyPartner;
use App\Models\CommissionTable;
use App\Models\PropertiesTable;
use App\Models\PropertyInquiry;
use App\Models\UsersTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * JSON API called exclusively by the n8n WhatsApp workflows (never by a
 * browser). Every write action carries the caller's WhatsApp `phone` number,
 * which is how we resolve *which* realtor is acting — WhatsApp itself is the
 * auth factor (n8n only relays messages from the number that sent them), and
 * the X-API-Key middleware on the route group proves the request came from
 * our n8n instance in the first place.
 */
class RealtorController extends Controller
{
    private function findByPhone(string $phone): ?UsersTable
    {
        return UsersTable::where('Phone', PhoneNormalizer::toLocal($phone))->first();
    }

    private function notFound(): \Illuminate\Http\JsonResponse
    {
        return response()->json(['status' => 'error', 'message' => 'No realtor account found for this phone number.'], 404);
    }

    // ── Registration ─────────────────────────────────────────────────────────

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'email'          => 'required|email|unique:users_tables,Email',
            'phone'          => 'required|string|min:10|max:15',
            'specialization' => 'nullable|string|max:100',
            'referral_code'  => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $localPhone = PhoneNormalizer::toLocal($request->phone);

        if (UsersTable::where('Phone', $localPhone)->exists()) {
            return response()->json(['status' => 'error', 'message' => 'This phone number is already registered.'], 422);
        }

        $agencyId = null;
        if ($request->filled('referral_code')) {
            $agency   = AgencyPartner::where('referral_code', $request->referral_code)->where('status', 'active')->first();
            $agencyId = $agency?->id;
        }

        $user = UsersTable::create([
            'First_name'     => $request->first_name,
            'Last_name'      => $request->last_name,
            'Email'          => $request->email,
            'Phone'          => $localPhone,
            'Password'       => Hash::make(Str::random(24)), // no WhatsApp "password" — reset flow covers web dashboard access
            'Specialization' => $request->specialization,
            'Status'         => 'pending',
            '2fa'            => 'disabled',
            'steps_signup'   => 'complete',
            'agency_id'      => $agencyId,
        ]);

        // api_token is guarded against mass assignment - set it explicitly.
        $user->forceFill(['api_token' => hash('sha256', Str::random(60))])->save();

        try {
            Mail::to($user->Email)->send(new WelcomeRealtor($user));
        } catch (\Throwable $e) {
            Log::error("WhatsApp register: WelcomeRealtor mail failed for realtor #{$user->id}: " . $e->getMessage());
        }

        $verifyUrl = URL::temporarySignedRoute('verification.verify', now()->addHours(48), ['id' => $user->id, 'hash' => sha1($user->Email)]);
        try {
            Mail::to($user->Email)->send(new EmailVerificationMail($user, $verifyUrl));
        } catch (\Throwable $e) {
            Log::error("WhatsApp register: EmailVerificationMail failed for realtor #{$user->id}: " . $e->getMessage());
        }

        $kycUrl = $this->generateKycLink($user);

        return response()->json([
            'status'  => 'success',
            'message' => 'Realtor registered. Awaiting KYC completion and admin approval.',
            'data'    => [
                'realtor_id'  => $user->id,
                'status'      => $user->Status,
                'kyc_link'    => $kycUrl,
                'kyc_expires' => 'in 48 hours',
            ],
        ], 201);
    }

    public function kycLink(Request $request)
    {
        $validator = Validator::make($request->all(), ['phone' => 'required|string']);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Phone number is required.'], 422);
        }

        $user = $this->findByPhone($request->phone);
        if (! $user) {
            return $this->notFound();
        }

        return response()->json([
            'status' => 'success',
            'data'   => ['kyc_link' => $this->generateKycLink($user), 'kyc_status' => $user->kyc_status],
        ]);
    }

    private function generateKycLink(UsersTable $user): string
    {
        return URL::temporarySignedRoute('whatsapp.kyc.show', now()->addHours(48), ['id' => $user->id]);
    }

    // ── Identify / "login" ───────────────────────────────────────────────────

    public function identify(Request $request)
    {
        $validator = Validator::make($request->all(), ['phone' => 'required|string']);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Phone number is required.'], 422);
        }

        $user = $this->findByPhone($request->phone);
        if (! $user) {
            return $this->notFound();
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'realtor_id'        => $user->id,
                'first_name'        => $user->First_name,
                'last_name'         => $user->Last_name,
                'status'            => $user->Status,
                'kyc_status'        => $user->kyc_status,
                'email_verified'    => (bool) $user->email_verified_at,
                'can_transact'      => $user->Status === 'active' && (bool) $user->email_verified_at,
            ],
        ]);
    }

    // ── Properties ────────────────────────────────────────────────────────────

    public function createProperty(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'       => 'required|string',
            'title'       => 'required|string|max:255',
            'type'        => 'required|string|max:100',
            'description' => 'required|string',
            'listing_type'=> 'required|string|max:50',
            'street'      => 'required|string|max:255',
            'neigborhood' => 'required|string|max:255',
            'city'        => 'required|string|max:255',
            'state'       => 'required|string|max:255',
            'localarea'   => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'amenities'   => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $realtor = $this->findByPhone($request->phone);
        if (! $realtor) {
            return $this->notFound();
        }

        if ($realtor->Status !== 'active') {
            return response()->json(['status' => 'error', 'message' => 'Your account must be approved before you can list a property.'], 403);
        }

        $property = PropertiesTable::create([
            'user_id'         => $realtor->id,
            'title'           => $request->title,
            'type'            => $request->type,
            'description'     => $request->description,
            'listing_type'    => $request->listing_type,
            'street'          => $request->street,
            'neigborhood'     => $request->neigborhood,
            'city'            => $request->city,
            'state'           => $request->state,
            'localarea'       => $request->localarea,
            'country'         => 'Nigeria',
            'amenities'       => $request->amenities ?? [],
            'price'           => $request->price,
            'photos'          => [],
            'videos'          => [],
            'listing_status'  => 'active',
            'approval_status' => 'pending',
            'views'           => '0',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Property created. Add photos to complete the listing.',
            'data'    => [
                'property_id'      => $property->id,
                'photo_upload_link'=> $this->generatePhotoLink($property),
            ],
        ], 201);
    }

    public function photoLink(Request $request, int $propertyId)
    {
        $validator = Validator::make($request->all(), ['phone' => 'required|string']);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Phone number is required.'], 422);
        }

        $realtor  = $this->findByPhone($request->phone);
        if (! $realtor) {
            return $this->notFound();
        }

        $property = PropertiesTable::where('user_id', $realtor->id)->find($propertyId);
        if (! $property) {
            return response()->json(['status' => 'error', 'message' => 'Property not found for this realtor.'], 404);
        }

        return response()->json(['status' => 'success', 'data' => ['photo_upload_link' => $this->generatePhotoLink($property)]]);
    }

    private function generatePhotoLink(PropertiesTable $property): string
    {
        return URL::temporarySignedRoute('whatsapp.property-photos.show', now()->addHours(48), ['id' => $property->id]);
    }

    public function updatePropertyStatus(Request $request, int $propertyId)
    {
        $validator = Validator::make($request->all(), [
            'phone'  => 'required|string',
            'status' => 'required|string|in:active,sold,rented,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $realtor  = $this->findByPhone($request->phone);
        if (! $realtor) {
            return $this->notFound();
        }

        $property = PropertiesTable::where('user_id', $realtor->id)->find($propertyId);
        if (! $property) {
            return response()->json(['status' => 'error', 'message' => 'Property not found for this realtor.'], 404);
        }

        $property->update(['listing_status' => $request->status]);

        return response()->json(['status' => 'success', 'message' => "Property marked as {$request->status}.", 'data' => ['property_id' => $property->id, 'listing_status' => $property->listing_status]]);
    }

    // ── Appointments ──────────────────────────────────────────────────────────

    public function appointments(Request $request)
    {
        $validator = Validator::make($request->all(), ['phone' => 'required|string']);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Phone number is required.'], 422);
        }

        $realtor = $this->findByPhone($request->phone);
        if (! $realtor) {
            return $this->notFound();
        }

        $appointments = PropertyInquiry::whereHas('property', fn ($q) => $q->where('user_id', $realtor->id))
            ->with('property:id,title')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderBy('preferred_date')
            ->get(['id', 'property_id', 'name', 'phone', 'preferred_date', 'preferred_time', 'status']);

        return response()->json(['status' => 'success', 'data' => $appointments]);
    }

    public function approveAppointment(Request $request, int $id)
    {
        $realtor = $this->findByPhone((string) $request->input('phone'));
        if (! $realtor) {
            return $this->notFound();
        }

        $apt = PropertyInquiry::whereHas('property', fn ($q) => $q->where('user_id', $realtor->id))->find($id);
        if (! $apt) {
            return response()->json(['status' => 'error', 'message' => 'Appointment not found for this realtor.'], 404);
        }

        $apt->update(['status' => 'confirmed']);

        return response()->json(['status' => 'success', 'message' => 'Appointment confirmed.']);
    }

    public function rejectAppointment(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), ['phone' => 'required|string', 'reason' => 'nullable|string|max:500']);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $realtor = $this->findByPhone($request->phone);
        if (! $realtor) {
            return $this->notFound();
        }

        $apt = PropertyInquiry::whereHas('property', fn ($q) => $q->where('user_id', $realtor->id))->find($id);
        if (! $apt) {
            return response()->json(['status' => 'error', 'message' => 'Appointment not found for this realtor.'], 404);
        }

        $apt->update(['status' => 'cancelled', 'cancel_reason' => $request->reason]);

        return response()->json(['status' => 'success', 'message' => 'Appointment rejected.']);
    }

    // ── Search ────────────────────────────────────────────────────────────────

    public function search(Request $request)
    {
        $query = PropertiesTable::where('approval_status', 'approved')->where('listing_status', 'active');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn ($w) => $w->where('title', 'like', "%{$q}%")->orWhere('city', 'like', "%{$q}%")->orWhere('neigborhood', 'like', "%{$q}%"));
        }
        if ($request->filled('state')) $query->where('state', $request->state);
        if ($request->filled('type'))  $query->where('type', $request->type);
        if ($request->filled('max_price')) $query->where('price', '<=', $request->max_price);

        $results = $query->latest()->take(10)->get(['id', 'title', 'type', 'city', 'state', 'price', 'photos']);

        return response()->json(['status' => 'success', 'data' => $results]);
    }

    // ── Stats ─────────────────────────────────────────────────────────────────

    public function stats(Request $request)
    {
        $validator = Validator::make($request->all(), ['phone' => 'required|string']);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Phone number is required.'], 422);
        }

        $realtor = $this->findByPhone($request->phone);
        if (! $realtor) {
            return $this->notFound();
        }

        $totalAssets    = PropertiesTable::where('user_id', $realtor->id)->count();
        $activeListings = PropertiesTable::where('user_id', $realtor->id)->where('listing_status', 'active')->where('approval_status', 'approved')->count();

        $listingEarned   = CommissionTable::where('listing_realtor_id', $realtor->id)->where('status', 'paid')->sum('listing_realtor_amount');
        $referringEarned = CommissionTable::where('referring_realtor_id', $realtor->id)->where('status', 'paid')->sum('referring_realtor_amount');

        return response()->json([
            'status' => 'success',
            'data'   => [
                'total_assets'    => $totalAssets,
                'active_listings' => $activeListings,
                'total_earnings'  => (float) ($listingEarned + $referringEarned),
            ],
        ]);
    }

    public function weeklyReport(Request $request)
    {
        $validator = Validator::make($request->all(), ['phone' => 'required|string']);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Phone number is required.'], 422);
        }

        $realtor = $this->findByPhone($request->phone);
        if (! $realtor) {
            return $this->notFound();
        }

        $since = now()->subDays(7);

        $sold   = PropertiesTable::where('state', $realtor->State)->where('listing_status', 'sold')->where('updated_at', '>=', $since)->count();
        $rented = PropertiesTable::where('state', $realtor->State)->where('listing_status', 'rented')->where('updated_at', '>=', $since)->count();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'state'         => $realtor->State,
                'period'        => 'last 7 days',
                'sold_count'    => $sold,
                'rented_count'  => $rented,
            ],
        ]);
    }
}
