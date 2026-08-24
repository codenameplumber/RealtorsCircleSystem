<?php

namespace App\Http\Controllers\Api\Whatsapp;

use App\Helpers\PhoneNormalizer;
use App\Http\Controllers\Controller;
use App\Mail\Agent\PropertyApprovedMail;
use App\Mail\Agent\PropertyRejectedMail;
use App\Mail\Agent\RealtorApprovedMail;
use App\Mail\Agent\RealtorRejectedMail;
use App\Models\AgentsTable;
use App\Models\PropertiesTable;
use App\Models\PropertyInquiry;
use App\Models\UsersTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

/**
 * JSON API called by n8n for the in-house (LGA) agent WhatsApp flows.
 * Agents are never self-registered — they only exist once Admin creates
 * them on the web dashboard — so there is no register() here, only identify.
 */
class AgentController extends Controller
{
    private function findByPhone(string $phone): ?AgentsTable
    {
        return AgentsTable::where('Phone', PhoneNormalizer::toLocal($phone))->first();
    }

    private function notFound(): \Illuminate\Http\JsonResponse
    {
        return response()->json(['status' => 'error', 'message' => 'No agent account found for this phone number.'], 404);
    }

    public function identify(Request $request)
    {
        $validator = Validator::make($request->all(), ['phone' => 'required|string']);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Phone number is required.'], 422);
        }

        $agent = $this->findByPhone($request->phone);
        if (! $agent) {
            return $this->notFound();
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'agent_id'      => $agent->id,
                'first_name'    => $agent->First_name,
                'assigned_area' => $agent->Assigned_area,
                'status'        => $agent->Status,
                'can_transact'  => $agent->Status === 'active',
            ],
        ]);
    }

    // ── Realtor approvals ────────────────────────────────────────────────────

    public function pendingRealtors(Request $request)
    {
        $agent = $this->requireActiveAgent($request);
        if ($agent instanceof \Illuminate\Http\JsonResponse) return $agent;

        $realtors = UsersTable::where('Lga', $agent->Assigned_area)
            ->where('kyc_status', 'pending')
            ->latest()
            ->get(['id', 'First_name', 'Last_name', 'Phone', 'Specialization', 'kyc_status', 'created_at']);

        return response()->json(['status' => 'success', 'data' => $realtors]);
    }

    public function approveRealtor(Request $request, int $id)
    {
        $agent = $this->requireActiveAgent($request);
        if ($agent instanceof \Illuminate\Http\JsonResponse) return $agent;

        $realtor = UsersTable::where('Lga', $agent->Assigned_area)->find($id);
        if (! $realtor) {
            return response()->json(['status' => 'error', 'message' => 'Realtor not found in your assigned area.'], 404);
        }

        $realtor->update(['Status' => 'active', 'kyc_status' => 'approved']);

        try {
            Mail::to($realtor->Email)->send(new RealtorApprovedMail($realtor));
        } catch (\Throwable $e) {
            Log::error("WhatsApp agent approve realtor mail failed #{$id}: " . $e->getMessage());
        }

        return response()->json(['status' => 'success', 'message' => "{$realtor->First_name} {$realtor->Last_name} approved."]);
    }

    public function rejectRealtor(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), ['phone' => 'required|string', 'reason' => 'required|string|min:10|max:500']);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $agent = $this->requireActiveAgent($request);
        if ($agent instanceof \Illuminate\Http\JsonResponse) return $agent;

        $realtor = UsersTable::where('Lga', $agent->Assigned_area)->find($id);
        if (! $realtor) {
            return response()->json(['status' => 'error', 'message' => 'Realtor not found in your assigned area.'], 404);
        }

        $realtor->update(['Status' => 'inactive', 'kyc_status' => 'rejected']);

        try {
            Mail::to($realtor->Email)->send(new RealtorRejectedMail($realtor, $request->reason));
        } catch (\Throwable $e) {
            Log::error("WhatsApp agent reject realtor mail failed #{$id}: " . $e->getMessage());
        }

        return response()->json(['status' => 'success', 'message' => "{$realtor->First_name} {$realtor->Last_name} rejected."]);
    }

    // ── Property approvals ───────────────────────────────────────────────────

    public function pendingProperties(Request $request)
    {
        $agent = $this->requireActiveAgent($request);
        if ($agent instanceof \Illuminate\Http\JsonResponse) return $agent;

        $properties = PropertiesTable::whereHas('user', fn ($q) => $q->where('Lga', $agent->Assigned_area))
            ->where('approval_status', 'pending')
            ->with('user:id,First_name,Last_name')
            ->latest()
            ->get(['id', 'user_id', 'title', 'city', 'price', 'created_at']);

        return response()->json(['status' => 'success', 'data' => $properties]);
    }

    public function approveProperty(Request $request, int $id)
    {
        $agent = $this->requireActiveAgent($request);
        if ($agent instanceof \Illuminate\Http\JsonResponse) return $agent;

        $property = PropertiesTable::whereHas('user', fn ($q) => $q->where('Lga', $agent->Assigned_area))->with('user')->find($id);
        if (! $property) {
            return response()->json(['status' => 'error', 'message' => 'Property not found in your assigned area.'], 404);
        }

        $property->update(['approval_status' => 'approved']);

        try {
            Mail::to($property->user->Email)->send(new PropertyApprovedMail($property));
        } catch (\Throwable $e) {
            Log::error("WhatsApp agent approve property mail failed #{$id}: " . $e->getMessage());
        }

        return response()->json(['status' => 'success', 'message' => "\"{$property->title}\" approved."]);
    }

    public function rejectProperty(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), ['phone' => 'required|string', 'reason' => 'required|string|min:10|max:500']);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $agent = $this->requireActiveAgent($request);
        if ($agent instanceof \Illuminate\Http\JsonResponse) return $agent;

        $property = PropertiesTable::whereHas('user', fn ($q) => $q->where('Lga', $agent->Assigned_area))->with('user')->find($id);
        if (! $property) {
            return response()->json(['status' => 'error', 'message' => 'Property not found in your assigned area.'], 404);
        }

        $property->update(['approval_status' => 'rejected']);

        try {
            Mail::to($property->user->Email)->send(new PropertyRejectedMail($property, $request->reason));
        } catch (\Throwable $e) {
            Log::error("WhatsApp agent reject property mail failed #{$id}: " . $e->getMessage());
        }

        return response()->json(['status' => 'success', 'message' => "\"{$property->title}\" rejected."]);
    }

    public function updatePropertyStatus(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'phone'  => 'required|string',
            'status' => 'required|string|in:active,sold,rented,inactive',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $agent = $this->requireActiveAgent($request);
        if ($agent instanceof \Illuminate\Http\JsonResponse) return $agent;

        $property = PropertiesTable::whereHas('user', fn ($q) => $q->where('Lga', $agent->Assigned_area))->find($id);
        if (! $property) {
            return response()->json(['status' => 'error', 'message' => 'Property not found in your assigned area.'], 404);
        }

        $property->update(['listing_status' => $request->status]);

        return response()->json(['status' => 'success', 'message' => "Property marked as {$request->status}."]);
    }

    // ── Appointments (notification feed) ────────────────────────────────────

    public function appointments(Request $request)
    {
        $agent = $this->requireActiveAgent($request);
        if ($agent instanceof \Illuminate\Http\JsonResponse) return $agent;

        $appointments = PropertyInquiry::whereHas('property.user', fn ($q) => $q->where('Lga', $agent->Assigned_area))
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->with('property:id,title')
            ->orderBy('preferred_date')
            ->get(['id', 'property_id', 'name', 'preferred_date', 'preferred_time', 'status']);

        return response()->json(['status' => 'success', 'data' => $appointments]);
    }

    // ── Weekly stats ──────────────────────────────────────────────────────────

    public function weeklyStats(Request $request)
    {
        $agent = $this->requireActiveAgent($request);
        if ($agent instanceof \Illuminate\Http\JsonResponse) return $agent;

        $since = now()->subDays(7);
        $lga   = $agent->Assigned_area;

        $newRealtors  = UsersTable::where('Lga', $lga)->where('created_at', '>=', $since)->count();
        $newProperties = PropertiesTable::whereHas('user', fn ($q) => $q->where('Lga', $lga))->where('created_at', '>=', $since)->count();
        $approvals    = PropertiesTable::whereHas('user', fn ($q) => $q->where('Lga', $lga))->where('approval_status', 'approved')->where('updated_at', '>=', $since)->count();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'lga'                => $lga,
                'period'             => 'last 7 days',
                'new_realtors'       => $newRealtors,
                'new_properties'     => $newProperties,
                'properties_approved'=> $approvals,
            ],
        ]);
    }

    private function requireActiveAgent(Request $request)
    {
        $validator = Validator::make($request->all(), ['phone' => 'required|string']);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Phone number is required.'], 422);
        }

        $agent = $this->findByPhone($request->phone);
        if (! $agent) {
            return $this->notFound();
        }

        if ($agent->Status !== 'active') {
            return response()->json(['status' => 'error', 'message' => 'Your agent account is not active.'], 403);
        }

        return $agent;
    }
}
