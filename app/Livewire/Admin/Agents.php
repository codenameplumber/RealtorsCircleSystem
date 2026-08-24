<?php

namespace App\Livewire\Admin;

use App\Data\NigeriaLocations;
use App\Mail\Admin\WelcomeAgentMail;
use App\Models\AdminAuditLog;
use App\Models\AgentsTable;
use App\Models\CommissionTable;
use App\Models\PropertiesTable;
use App\Models\UsersTable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Agents extends Component
{
    use WithPagination;

    // Filters
    public string $search       = '';
    public string $statusFilter = '';
    public string $stateFilter  = '';
    public string $lgaFilter    = '';
    public string $topState     = '';

    // Add agent form
    public bool   $showAddModal  = false;
    public string $first_name    = '';
    public string $last_name     = '';
    public string $email         = '';
    public string $password      = '';
    public string $phone         = '';
    public string $state         = '';
    public string $lga           = '';
    public string $specialization = '';

    // Profile modal
    public bool  $showProfile   = false;
    public ?int  $profileId     = null;

    public function updatingSearch()       { $this->resetPage(); }
    public function updatingStatusFilter() { $this->resetPage(); }
    public function updatingStateFilter()  { $this->lgaFilter = ''; $this->resetPage(); }
    public function updatingLgaFilter()    { $this->resetPage(); }
    public function updatingState()        { $this->lga = ''; }

    public function openAdd()    { $this->showAddModal = true; }
    public function closeAdd()   { $this->showAddModal = false; $this->resetForm(); }

    public function addAgent()
    {
        $this->validate([
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'email'           => 'required|email|unique:agents_tables,Email',
            'password'        => 'required|string|min:8',
            'phone'           => 'required|string|max:20',
            'state'           => 'required|string',
            'lga'             => 'required|string',
            'specialization'  => 'required|string',
        ]);

        $plain = $this->password;

        $agent = AgentsTable::create([
            'First_name'     => $this->first_name,
            'Last_name'      => $this->last_name,
            'Email'          => $this->email,
            'Password'       => Hash::make($plain),
            'Phone'          => $this->phone,
            'State'          => $this->state,
            'Assigned_area'  => $this->lga,
            'Specialization' => $this->specialization,
            'Status'         => 'active',
            'api_token'      => Str::random(60),
        ]);

        try {
            Mail::to($agent->Email)->send(new WelcomeAgentMail(
                agent:         $agent,
                plainPassword: $plain,
                loginUrl:      route('agent.login'),
            ));
        } catch (\Throwable $e) {
            Log::error('WelcomeAgentMail failed: ' . $e->getMessage());
        }

        AdminAuditLog::record('agent.create', 'AgentsTable', $agent->id, "Created agent {$agent->First_name} {$agent->Last_name} ({$this->lga})");

        $this->closeAdd();
        session()->flash('agent_success', 'Agent added successfully. A welcome email has been sent.');
    }

    public function viewProfile(int $id)
    {
        $this->profileId   = $id;
        $this->showProfile = true;
    }

    public function closeProfile()
    {
        $this->showProfile = false;
        $this->profileId   = null;
    }

    public function toggleStatus(int $id)
    {
        $agent     = AgentsTable::findOrFail($id);
        $newStatus = $agent->Status === 'active' ? 'inactive' : 'active';
        $agent->update(['Status' => $newStatus]);

        AdminAuditLog::record(
            $newStatus === 'active' ? 'agent.activate' : 'agent.suspend',
            'AgentsTable',
            $id,
            "Agent {$agent->First_name} {$agent->Last_name} status changed to {$newStatus}"
        );
    }

    private function resetForm()
    {
        $this->first_name = $this->last_name = $this->email = $this->phone = $this->password = '';
        $this->state      = $this->lga        = $this->specialization = '';
    }

    public function render()
    {
        $agents = AgentsTable::query()
            ->when($this->search, fn($q) => $q->where(fn($q2) =>
                $q2->where('First_name', 'like', "%{$this->search}%")
                   ->orWhere('Last_name',  'like', "%{$this->search}%")
                   ->orWhere('Email',      'like', "%{$this->search}%")
            ))
            ->when($this->statusFilter, fn($q) => $q->where('Status', $this->statusFilter))
            ->when($this->stateFilter,  fn($q) => $q->where('State', $this->stateFilter))
            ->when($this->lgaFilter,    fn($q) => $q->where('Assigned_area', $this->lgaFilter))
            ->latest()
            ->paginate(15);

        // Stat board — respects current search/location filters
        $statsBase = AgentsTable::query()
            ->when($this->search, fn($q) => $q->where(fn($q2) =>
                $q2->where('First_name', 'like', "%{$this->search}%")
                   ->orWhere('Last_name',  'like', "%{$this->search}%")
                   ->orWhere('Email',      'like', "%{$this->search}%")
            ))
            ->when($this->stateFilter, fn($q) => $q->where('State', $this->stateFilter))
            ->when($this->lgaFilter,   fn($q) => $q->where('Assigned_area', $this->lgaFilter));

        $stats = [
            'total'    => (clone $statsBase)->count(),
            'active'   => (clone $statsBase)->where('Status', 'active')->count(),
            'inactive' => (clone $statsBase)->whereIn('Status', ['inactive', 'suspended'])->count(),
        ];

        $topAgents = AgentsTable::query()
            ->when($this->topState, fn($q) => $q->where('State', $this->topState))
            ->withCount(['commissions as deals_count' => fn($q) => $q->where('status', 'paid')])
            ->withSum(['commissions as total_value'   => fn($q) => $q->where('status', 'paid')], 'total_amount')
            ->orderByDesc('deals_count')
            ->take(10)
            ->get();

        $profileAgent = null;
        $profileStats = null;
        if ($this->profileId) {
            $profileAgent = AgentsTable::find($this->profileId);
            if ($profileAgent) {
                $profileStats = [
                    'area_realtors'   => UsersTable::where('Lga', $profileAgent->Assigned_area)->count(),
                    'area_properties' => PropertiesTable::whereHas('user', fn($q) => $q->where('Lga', $profileAgent->Assigned_area))->count(),
                    'deals_closed'    => CommissionTable::where('agent_id', $profileAgent->id)->where('status', 'paid')->count(),
                    'agent_fee_total' => CommissionTable::where('agent_id', $profileAgent->id)->where('status', 'paid')->sum('agent_fee'),
                    'deals_pending'   => CommissionTable::where('agent_id', $profileAgent->id)->where('status', 'pending')->count(),
                ];
            }
        }

        return view('livewire.admin.agents', [
            'agents'       => $agents,
            'topAgents'    => $topAgents,
            'profileAgent' => $profileAgent,
            'profileStats' => $profileStats,
            'states'       => NigeriaLocations::states(),
            'lgas'         => $this->state ? NigeriaLocations::lgasFor($this->state) : [],
            'filterLgas'   => $this->stateFilter ? NigeriaLocations::lgasFor($this->stateFilter) : [],
            'stats'        => $stats,
            'specializations' => ['Residential', 'Commercial', 'Land', 'Industrial', 'Mixed'],
        ]);
    }
}
