<?php

namespace App\Services;

use App\Models\CommissionSetting;

class CommissionSplitCalculator
{
    /**
     * Compute each party's share of a commission pool based on the persisted
     * split-percentage settings for the chosen model.
     *
     * Model 1 — Standard deal: listing realtor + referring realtor + agent + platform
     * Model 2 — Direct deal:   listing realtor + agent + platform (no referring realtor)
     *
     * @return array{listing_realtor_amount: float, referring_realtor_amount: float, agent_fee: float, platform_fee: float}
     */
    public static function split(int $model, float $totalAmount): array
    {
        $s = CommissionSetting::current();

        if ($model === 1) {
            return [
                'listing_realtor_amount'   => round($totalAmount * $s->m1_listing_pct / 100, 2),
                'referring_realtor_amount' => round($totalAmount * $s->m1_referring_pct / 100, 2),
                'agent_fee'                => round($totalAmount * $s->m1_agent_pct / 100, 2),
                'platform_fee'             => round($totalAmount * $s->m1_platform_pct / 100, 2),
            ];
        }

        return [
            'listing_realtor_amount'   => round($totalAmount * $s->m2_listing_pct / 100, 2),
            'referring_realtor_amount' => 0,
            'agent_fee'                => round($totalAmount * $s->m2_agent_pct / 100, 2),
            'platform_fee'             => round($totalAmount * $s->m2_platform_pct / 100, 2),
        ];
    }
}
