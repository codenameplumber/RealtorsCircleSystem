<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\RealtorRating;
use App\Models\UsersTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Public realtor ratings.
 *
 * Deduplication used to key on session()->getId() alone, which any visitor
 * defeats by clearing cookies — ratings could be inflated without limit. We now
 * key on the session *and* a hash of the client IP, so a fresh session from the
 * same address is still recognised as a repeat vote. The IP is stored only as a
 * salted hash: enough to compare against, never enough to recover the address.
 */
class RealtorRatingController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'realtor_id' => 'required|integer|exists:users_tables,id',
            'rating'     => 'required|integer|min:1|max:5',
        ]);

        $realtorId = (int) $validated['realtor_id'];

        // Only approved, active realtors can be rated — otherwise a pending or
        // suspended account can be seeded with a reputation before review.
        $rateable = UsersTable::where('id', $realtorId)->where('Status', 'active')->exists();

        if (! $rateable) {
            return response()->json(['message' => 'This realtor is not available for rating.'], 404);
        }

        $sessionToken = $request->session()->getId() . '_' . $realtorId;
        $ipHash       = $this->ipHash($request, $realtorId);

        $alreadyRated = RealtorRating::where('realtor_id', $realtorId)
            ->where(function ($q) use ($sessionToken, $ipHash) {
                $q->where('session_token', $sessionToken)->orWhere('ip_hash', $ipHash);
            })
            ->exists();

        if ($alreadyRated) {
            return response()->json(['message' => 'You have already rated this realtor.']);
        }

        RealtorRating::create([
            'realtor_id'    => $realtorId,
            'rating'        => (int) $validated['rating'],
            'session_token' => $sessionToken,
            'ip_hash'       => $ipHash,
        ]);

        return response()->json(['message' => 'Rating saved. Thank you!']);
    }

    /**
     * Salted with APP_KEY so the stored hashes are not reversible by rainbow
     * table — the IPv4 space is small enough that a bare sha1 would be.
     */
    private function ipHash(Request $request, int $realtorId): string
    {
        return hash_hmac('sha256', $request->ip() . '_' . $realtorId, config('app.key'));
    }
}
