<?php

namespace App\Http\Controllers\Api\Whatsapp;

use App\Helpers\PhoneNormalizer;
use App\Http\Controllers\Controller;
use App\Models\AgencyPartner;
use App\Models\CommissionTable;
use App\Models\UsersTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * JSON API called by n8n for the agency-partner WhatsApp flows. Agencies are
 * admin-provisioned only (same as agents) — no register() here.
 */
class AgencyController extends Controller
{
    private function findByPhone(string $phone): ?AgencyPartner
    {
        return AgencyPartner::where('phone', PhoneNormalizer::toLocal($phone))->first();
    }

    private function notFound(): \Illuminate\Http\JsonResponse
    {
        return response()->json(['status' => 'error', 'message' => 'No agency account found for this phone number.'], 404);
    }

    private function requireActiveAgency(Request $request)
    {
        $validator = Validator::make($request->all(), ['phone' => 'required|string']);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Phone number is required.'], 422);
        }

        $agency = $this->findByPhone($request->phone);
        if (! $agency) {
            return $this->notFound();
        }

        if ($agency->status !== 'active') {
            return response()->json(['status' => 'error', 'message' => 'Your agency account is not active.'], 403);
        }

        return $agency;
    }

    public function identify(Request $request)
    {
        $validator = Validator::make($request->all(), ['phone' => 'required|string']);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Phone number is required.'], 422);
        }

        $agency = $this->findByPhone($request->phone);
        if (! $agency) {
            return $this->notFound();
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'agency_id'     => $agency->id,
                'company_name'  => $agency->company_name,
                'status'        => $agency->status,
                'referral_code' => $agency->referral_code,
                'can_transact'  => $agency->status === 'active',
            ],
        ]);
    }

    public function network(Request $request)
    {
        $agency = $this->requireActiveAgency($request);
        if ($agency instanceof \Illuminate\Http\JsonResponse) return $agency;

        $totalRealtors  = UsersTable::where('agency_id', $agency->id)->count();
        $activeRealtors = UsersTable::where('agency_id', $agency->id)->where('Status', 'active')->count();

        return response()->json([
            'status' => 'success',
            'data'   => ['total_realtors' => $totalRealtors, 'active_realtors' => $activeRealtors],
        ]);
    }

    public function earnings(Request $request)
    {
        $agency = $this->requireActiveAgency($request);
        if ($agency instanceof \Illuminate\Http\JsonResponse) return $agency;

        $totalEarned = CommissionTable::where('agency_id', $agency->id)->where('status', 'paid')->sum('agency_override_amount');
        $pending     = CommissionTable::where('agency_id', $agency->id)->where('status', 'pending')->sum('agency_override_amount');
        $dealsCount  = CommissionTable::where('agency_id', $agency->id)->count();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'total_earned'  => (float) $totalEarned,
                'pending'       => (float) $pending,
                'deals_count'   => $dealsCount,
            ],
        ]);
    }

    public function weeklyStats(Request $request)
    {
        $agency = $this->requireActiveAgency($request);
        if ($agency instanceof \Illuminate\Http\JsonResponse) return $agency;

        $since = now()->subDays(7);

        $newRealtors = UsersTable::where('agency_id', $agency->id)->where('created_at', '>=', $since)->count();
        $dealsClosed = CommissionTable::where('agency_id', $agency->id)->where('created_at', '>=', $since)->count();
        $overrideEarned = CommissionTable::where('agency_id', $agency->id)->where('created_at', '>=', $since)->sum('agency_override_amount');

        return response()->json([
            'status' => 'success',
            'data'   => [
                'period'          => 'last 7 days',
                'new_realtors'    => $newRealtors,
                'deals_closed'    => $dealsClosed,
                'override_earned' => (float) $overrideEarned,
            ],
        ]);
    }
}
