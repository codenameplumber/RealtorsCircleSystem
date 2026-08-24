<?php

namespace App\Livewire\Agent;

use App\Models\AgentsTable;
use App\Models\CommissionTable;
use App\Models\PropertiesTable;
use App\Models\PropertyInquiry;
use App\Models\UsersTable;
use Livewire\Component;

class Reports extends Component
{
    public AgentsTable $agent;
    public string $period = 'week';

    public function mount(): void
    {
        $this->agent = AgentsTable::findOrFail(session('agent_id'));
    }

    public function render()
    {
        $lga = $this->agent->Assigned_area;

        $from = match ($this->period) {
            'today' => now()->startOfDay(),
            'month' => now()->startOfMonth(),
            default => now()->startOfWeek(),
        };

        try {
            $periodStats = [
                'new_requests'        => PropertyInquiry::whereHas('property.user', fn ($q) => $q->where('Lga', $lga))
                    ->where('created_at', '>=', $from)->count(),
                'closed_deals'        => PropertyInquiry::whereHas('property.user', fn ($q) => $q->where('Lga', $lga))
                    ->where('status', 'completed')->where('updated_at', '>=', $from)->count(),
                'approved_properties' => PropertiesTable::whereHas('user', fn ($q) => $q->where('Lga', $lga))
                    ->where('approval_status', 'approved')->where('updated_at', '>=', $from)->count(),
                'approved_realtors'   => UsersTable::where('Lga', $lga)
                    ->where('kyc_status', 'approved')->where('updated_at', '>=', $from)->count(),
                'commission_earned'   => (float) CommissionTable::where('agent_id', $this->agent->id)
                    ->where('created_at', '>=', $from)->sum('agent_fee'),
            ];

            $allTimeStats = [
                'total_realtors'    => UsersTable::where('Lga', $lga)->where('Status', 'active')->count(),
                'total_properties'  => PropertiesTable::whereHas('user', fn ($q) => $q->where('Lga', $lga))->count(),
                'listed_properties' => PropertiesTable::whereHas('user', fn ($q) => $q->where('Lga', $lga))
                    ->where('approval_status', 'approved')->count(),
                'total_deals'       => PropertyInquiry::whereHas('property.user', fn ($q) => $q->where('Lga', $lga))
                    ->where('status', 'completed')->count(),
                'total_commission'  => (float) CommissionTable::where('agent_id', $this->agent->id)->sum('agent_fee'),
            ];
        } catch (\Throwable) {
            $zero        = ['new_requests' => 0, 'closed_deals' => 0, 'approved_properties' => 0, 'approved_realtors' => 0, 'commission_earned' => 0];
            $periodStats = $zero;
            $allTimeStats = ['total_realtors' => 0, 'total_properties' => 0, 'listed_properties' => 0, 'total_deals' => 0, 'total_commission' => 0];
        }

        return view('livewire.agent.reports', compact('periodStats', 'allTimeStats'));
    }
}
