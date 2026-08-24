<?php

namespace App\Livewire\Agent;

use App\Models\AgentsTable;
use App\Models\CommissionTable;
use App\Models\PropertiesTable;
use App\Models\PropertyInquiry;
use App\Models\UsersTable;
use Livewire\Component;

class Overview extends Component
{
    public AgentsTable $agent;

    public function mount(): void
    {
        $this->agent = AgentsTable::findOrFail(session('agent_id'));
    }

    public function render()
    {
        $lga = $this->agent->Assigned_area;

        try {
            $stats = [
                'pending_realtors'     => UsersTable::where('Lga', $lga)->where('kyc_status', 'pending')->count(),
                'active_realtors'      => UsersTable::where('Lga', $lga)->where('Status', 'active')->count(),
                'total_properties'     => PropertiesTable::whereHas('user', fn ($q) => $q->where('Lga', $lga))->count(),
                'pending_properties'   => PropertiesTable::whereHas('user', fn ($q) => $q->where('Lga', $lga))->where('approval_status', 'pending')->count(),
                'pending_appointments' => PropertyInquiry::whereHas('property.user', fn ($q) => $q->where('Lga', $lga))->where('status', 'pending')->count(),
                'commission_earned'    => (float) CommissionTable::where('agent_id', $this->agent->id)->sum('agent_fee'),
            ];

            $recentAppointments = PropertyInquiry::whereHas('property.user', fn ($q) => $q->where('Lga', $lga))
                ->with(['property', 'property.user'])
                ->where('status', 'pending')
                ->latest()
                ->take(5)
                ->get();

            $recentRealtors = UsersTable::where('Lga', $lga)
                ->where('kyc_status', 'pending')
                ->latest()
                ->take(5)
                ->get();
        } catch (\Throwable $e) {
            $stats              = array_fill_keys(['pending_realtors', 'active_realtors', 'total_properties', 'pending_properties', 'pending_appointments', 'commission_earned'], 0);
            $recentAppointments = collect();
            $recentRealtors     = collect();
        }

        return view('livewire.agent.overview', compact('stats', 'recentAppointments', 'recentRealtors'));
    }
}
