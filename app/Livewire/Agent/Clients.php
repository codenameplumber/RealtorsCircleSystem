<?php

namespace App\Livewire\Agent;

use App\Models\AgentsTable;
use App\Models\PropertyInquiry;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Clients extends Component
{
    use WithPagination;

    public AgentsTable $agent;
    public string $search = '';

    public function mount(): void
    {
        $this->agent = AgentsTable::findOrFail(session('agent_id'));
    }

    public function updatedSearch(): void { $this->resetPage(); }

    public function render()
    {
        try {
            $query = PropertyInquiry::whereHas('property.user', fn ($q) => $q->where('Lga', $this->agent->Assigned_area))
                ->select(
                    'name',
                    'email',
                    'phone',
                    DB::raw('MAX(created_at) as last_inquiry'),
                    DB::raw('COUNT(*) as total_inquiries'),
                )
                ->groupBy('email', 'name', 'phone');

            if ($this->search) {
                $query->where(fn ($q) => $q
                    ->where('name',  'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%")
                );
            }

            $clients = $query->orderByDesc('last_inquiry')->paginate(20);
        } catch (\Throwable $e) {
            $clients = collect()->paginate(20);
        }

        return view('livewire.agent.clients', compact('clients'));
    }
}
