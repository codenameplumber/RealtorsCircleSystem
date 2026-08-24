<?php

namespace App\Livewire\Admin;

use App\Data\NigeriaLocations;
use App\Models\AgentsTable;
use App\Models\CommissionTable;
use App\Models\PropertiesTable;
use App\Models\PropertyInquiry;
use App\Models\UsersTable;
use Livewire\Component;

class Overview extends Component
{
    public string $selectedState = '';
    public string $selectedLga   = '';

    public function updatingSelectedState() { $this->selectedLga = ''; }

    public function render()
    {
        $rQ = UsersTable::query()
            ->when($this->selectedState, fn($q) => $q->where('State', $this->selectedState))
            ->when($this->selectedLga,   fn($q) => $q->where('Lga', $this->selectedLga));
        $aQ = AgentsTable::query()
            ->when($this->selectedState, fn($q) => $q->where('State', $this->selectedState))
            ->when($this->selectedLga,   fn($q) => $q->where('Assigned_area', $this->selectedLga));
        $pQ = PropertiesTable::query()
            ->when($this->selectedState, fn($q) => $q->where('state', $this->selectedState))
            ->when($this->selectedLga,   fn($q) => $q->where('localarea', $this->selectedLga));

        $stats = [
            'total_realtors'     => (clone $rQ)->count(),
            'active_realtors'    => (clone $rQ)->where('Status', 'active')->count(),
            'pending_realtors'   => (clone $rQ)->where('Status', 'pending')->count(),
            'suspended_realtors' => (clone $rQ)->where('Status', 'suspended')->count(),
            'total_agents'       => (clone $aQ)->count(),
            'active_agents'      => (clone $aQ)->where('Status', 'active')->count(),
            'total_properties'   => (clone $pQ)->count(),
            'approved_properties'=> (clone $pQ)->where('approval_status', 'approved')->count(),
            'pending_properties' => (clone $pQ)->where('approval_status', 'pending')->count(),
            'total_revenue'      => CommissionTable::where('status', 'paid')->sum('total_amount'),
            'closed_deals'       => CommissionTable::where('status', 'paid')->count(),
            'pending_deals'      => CommissionTable::where('status', 'pending')->count(),
            'total_inquiries'    => PropertyInquiry::count(),
        ];

        $realtorFilters = array_filter([
            'State' => $this->selectedState ?: null,
            'Lga'   => $this->selectedLga ?: null,
        ]);
        $agentFilters = array_filter([
            'State'         => $this->selectedState ?: null,
            'Assigned_area' => $this->selectedLga ?: null,
        ]);

        $monthlyRealtors = $this->getMonthly(UsersTable::class, $realtorFilters);
        $monthlyAgents   = $this->getMonthly(AgentsTable::class, $agentFilters);

        $recentRealtors = (clone $rQ)->latest()->take(5)->get();
        $recentAgents   = (clone $aQ)->latest()->take(5)->get();

        return view('livewire.admin.overview', compact(
            'stats', 'monthlyRealtors', 'monthlyAgents', 'recentRealtors', 'recentAgents'
        ))->with([
            'states' => NigeriaLocations::states(),
            'lgas'   => $this->selectedState ? NigeriaLocations::lgasFor($this->selectedState) : [],
        ]);
    }

    private function getMonthly(string $model, array $filters): array
    {
        $data    = array_fill(0, 12, 0);
        $records = $model::whereYear('created_at', now()->year);
        foreach ($filters as $col => $val) {
            $records->where($col, $val);
        }
        $records = $records->selectRaw('MONTH(created_at) as month, COUNT(*) as total')->groupBy('month')->get();
        foreach ($records as $r) {
            $data[$r->month - 1] = (int) $r->total;
        }
        return $data;
    }
}
