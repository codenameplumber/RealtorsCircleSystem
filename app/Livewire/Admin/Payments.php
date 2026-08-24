<?php

namespace App\Livewire\Admin;

use App\Data\NigeriaLocations;
use App\Models\PayoutRequest;
use Livewire\Component;

class Payments extends Component
{
    public string $stateFilter = '';
    public string $lgaFilter   = '';

    public function updatingStateFilter() { $this->lgaFilter = ''; }

    private function locationOf(PayoutRequest $r): array
    {
        if ($r->realtor_id && $r->realtor) {
            return ['state' => $r->realtor->State, 'lga' => $r->realtor->Lga];
        }

        if ($r->agent_id && $r->agent) {
            return ['state' => $r->agent->State, 'lga' => null];
        }

        return ['state' => null, 'lga' => null];
    }

    private function requesterKey(PayoutRequest $r): string
    {
        return $r->realtor_id ? "realtor-{$r->realtor_id}" : "agent-{$r->agent_id}";
    }

    public function render()
    {
        $all = PayoutRequest::with([
            'realtor:id,First_name,Last_name,State,Lga',
            'agent:id,First_name,Last_name,State',
        ])->get();

        $filtered = $all->filter(function (PayoutRequest $r) {
            $loc = $this->locationOf($r);

            if ($this->stateFilter && $loc['state'] !== $this->stateFilter) {
                return false;
            }

            if ($this->lgaFilter && $loc['lga'] !== $this->lgaFilter) {
                return false;
            }

            return true;
        });

        $summary = [
            'requesters'      => $filtered->map(fn ($r) => $this->requesterKey($r))->unique()->count(),
            'total_requested' => $filtered->sum('amount'),
            'total_paid'      => $filtered->where('status', 'paid')->sum('amount'),
            'total_pending'   => $filtered->whereIn('status', ['pending', 'processing'])->sum('amount'),
            'total_rejected'  => $filtered->where('status', 'rejected')->sum('amount'),
        ];

        $breakdown = collect(['pending', 'processing', 'paid', 'rejected'])->map(fn ($status) => (object) [
            'status' => $status,
            'count'  => $filtered->where('status', $status)->count(),
            'amount' => $filtered->where('status', $status)->sum('amount'),
        ]);

        // Leaderboard: by state, or by LGA within a chosen state
        $groupByLga = (bool) $this->stateFilter;
        $locations = $all
            ->filter(fn (PayoutRequest $r) => ! $groupByLga || $this->locationOf($r)['state'] === $this->stateFilter)
            ->groupBy(function (PayoutRequest $r) use ($groupByLga) {
                $loc = $this->locationOf($r);
                return $groupByLga ? ($loc['lga'] ?: 'Unknown') : ($loc['state'] ?: 'Unknown');
            })
            ->map(fn ($group, $name) => (object) [
                'name'       => $name,
                'requesters' => $group->map(fn ($r) => $this->requesterKey($r))->unique()->count(),
                'total'      => $group->sum('amount'),
                'count'      => $group->count(),
            ])
            ->sortByDesc('total')
            ->take(10)
            ->values();

        return view('livewire.admin.payments', compact('summary', 'breakdown', 'locations', 'groupByLga'))
            ->with([
                'states' => NigeriaLocations::states(),
                'lgas'   => $this->stateFilter ? NigeriaLocations::lgasFor($this->stateFilter) : [],
            ]);
    }
}
