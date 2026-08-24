<?php

namespace App\Livewire\Agent;

use App\Mail\AppointmentStatusMail;
use App\Models\AgencyPartner;
use App\Models\AgentsTable;
use App\Models\BankInformation;
use App\Models\CommissionSetting;
use App\Models\CommissionTable;
use App\Models\PropertyInquiry;
use App\Models\UsersTable;
use App\Services\CommissionSplitCalculator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Appointments extends Component
{
    use WithPagination;

    public AgentsTable $agent;

    public string $statusFilter = 'all';

    public bool $detailOpen = false;
    public ?int $viewingId  = null;

    public bool   $cancelOpen   = false;
    public ?int   $cancelId     = null;
    public string $cancelReason = '';

    public bool    $logDealOpen        = false;
    public ?int    $logDealId          = null;
    public ?int    $dealModel          = null;
    public string  $dealAmount         = '';
    public ?int    $referringRealtorId = null;

    public string $referringSearch  = '';
    public bool   $referringSearched = false;
    public ?array $referringMatch    = null;

    public function mount(): void
    {
        $this->agent = AgentsTable::findOrFail(session('agent_id'));
    }

    public function updatedStatusFilter(): void { $this->resetPage(); }

    // ── Detail modal ──────────────────────────────────────────────────────────

    public function viewDetail(int $id): void
    {
        $this->viewingId  = $id;
        $this->detailOpen = true;
    }

    public function closeDetail(): void
    {
        $this->detailOpen = false;
        $this->viewingId  = null;
    }

    // ── Confirm ───────────────────────────────────────────────────────────────

    public function confirm(int $id): void
    {
        try {
            $apt = $this->findInArea($id);
            $apt->update(['status' => 'confirmed']);

            $this->notifyParties($apt, 'confirmed');

            $this->closeDetail();
            $this->dispatch('agent-notify', message: 'Appointment confirmed. Client and realtor notified.', type: 'success');
        } catch (\Throwable $e) {
            Log::error("Appointment confirm failed #{$id}: " . $e->getMessage());
            $this->dispatch('agent-notify', message: 'Failed to confirm appointment.', type: 'error');
        }
    }

    // ── Mark Complete ─────────────────────────────────────────────────────────

    public function complete(int $id): void
    {
        try {
            $apt = $this->findInArea($id);
            $apt->update(['status' => 'completed']);

            $this->notifyParties($apt, 'completed');

            $this->dispatch('agent-notify', message: 'Appointment marked as completed. Both parties notified.', type: 'success');
        } catch (\Throwable $e) {
            Log::error("Appointment complete failed #{$id}: " . $e->getMessage());
            $this->dispatch('agent-notify', message: 'Failed to update appointment.', type: 'error');
        }
    }

    // ── Cancel ────────────────────────────────────────────────────────────────

    public function openCancel(int $id): void
    {
        $this->cancelId     = $id;
        $this->cancelReason = '';
        $this->resetErrorBag();
        $this->cancelOpen   = true;
    }

    public function submitCancel(): void
    {
        try {
            $apt = $this->findInArea($this->cancelId);
            $apt->update([
                'status'        => 'cancelled',
                'cancel_reason' => $this->cancelReason ?: null,
            ]);

            $this->notifyParties($apt, 'cancelled', $this->cancelReason ?: null);

            $this->cancelOpen   = false;
            $this->cancelId     = null;
            $this->cancelReason = '';
            $this->closeDetail();
            $this->dispatch('agent-notify', message: 'Appointment cancelled. Client and realtor notified.', type: 'success');
        } catch (\Throwable $e) {
            Log::error("Appointment cancel failed #{$this->cancelId}: " . $e->getMessage());
            $this->dispatch('agent-notify', message: 'Failed to cancel appointment.', type: 'error');
        }
    }

    // ── Log Deal (creates the commission record) ─────────────────────────────

    public function openLogDeal(int $id): void
    {
        $apt = $this->findInArea($id);

        $this->logDealId          = $apt->id;
        $this->dealModel          = null;
        $this->dealAmount         = '';
        $this->referringRealtorId = null;
        $this->referringSearch    = '';
        $this->referringSearched  = false;
        $this->referringMatch     = null;
        $this->resetErrorBag();
        $this->logDealOpen = true;
    }

    public function updatedDealModel(): void
    {
        if ($this->dealModel !== 1) {
            $this->clearReferringMatch();
        }
    }

    /**
     * Look up a realtor by exact email or phone so the agent can confirm identity
     * (full name, location, bank status) before attributing a referral to them.
     */
    public function searchReferringRealtor(): void
    {
        $this->resetErrorBag('referringRealtorId');
        $this->referringSearched = true;
        $this->referringMatch    = null;
        $this->referringRealtorId = null;

        $term = trim($this->referringSearch);

        if ($term === '') {
            $this->referringSearched = false;
            return;
        }

        $realtor = UsersTable::where('Status', 'active')
            ->where(fn ($q) => $q->where('Email', $term)->orWhere('Phone', $term))
            ->first();

        if (! $realtor) {
            return;
        }

        $bank = BankInformation::where('user_id', $realtor->id)->first();

        $this->referringRealtorId = $realtor->id;
        $this->referringMatch = [
            'id'           => $realtor->id,
            'name'         => trim("{$realtor->First_name} {$realtor->Last_name}"),
            'email'        => $realtor->Email,
            'phone'        => $realtor->Phone,
            'state'        => $realtor->State,
            'city'         => $realtor->Local_area ?: $realtor->Lga,
            'has_bank'     => (bool) $bank,
            'bank_status'  => $bank?->status,
        ];
    }

    public function clearReferringMatch(): void
    {
        $this->referringSearch    = '';
        $this->referringSearched  = false;
        $this->referringMatch     = null;
        $this->referringRealtorId = null;
        $this->resetErrorBag('referringRealtorId');
    }

    public function closeLogDeal(): void
    {
        $this->logDealOpen        = false;
        $this->logDealId          = null;
        $this->dealModel          = null;
        $this->dealAmount         = '';
        $this->referringRealtorId = null;
        $this->referringSearch    = '';
        $this->referringSearched  = false;
        $this->referringMatch     = null;
        $this->resetErrorBag();
    }

    public function submitLogDeal(): void
    {
        try {
            $apt = $this->findInArea($this->logDealId);

            $this->validate([
                'dealModel'  => ['required', 'in:1,2'],
                'dealAmount' => ['required', 'numeric', 'min:1'],
                'referringRealtorId' => $this->dealModel === 1
                    ? ['required', 'integer', 'exists:users_tables,id']
                    : ['nullable'],
            ], [
                'referringRealtorId.required' => 'Search for and confirm the referring realtor for a Model 1 deal.',
            ]);

            $listingRealtorId = $apt->property?->user_id;

            if ($this->dealModel === 1 && (int) $this->referringRealtorId === (int) $listingRealtorId) {
                $this->addError('referringRealtorId', 'The referring realtor must be different from the listing realtor.');
                return;
            }

            $totalAmount = (float) $this->dealAmount;
            $splits      = CommissionSplitCalculator::split($this->dealModel, $totalAmount);

            do {
                $reference = 'RC-' . now()->format('ymd') . '-' . strtoupper(Str::random(5));
            } while (CommissionTable::where('deal_reference', $reference)->exists());

            // Compute agency override if the listing realtor belongs to an agency
            $listingRealtor   = $apt->property?->user;
            $agencyId         = $listingRealtor?->agency_id;
            $agencyOverride   = 0;
            $netPlatformFee   = $splits['platform_fee'];

            if ($agencyId) {
                $overridePct   = CommissionSetting::current()->agency_override_pct ?? 0;
                $agencyOverride = round($splits['platform_fee'] * $overridePct / 100, 2);
                $netPlatformFee = round($splits['platform_fee'] - $agencyOverride, 2);
            }

            $commission = CommissionTable::create([
                'property_id'              => $apt->property_id,
                'listing_realtor_id'       => $listingRealtorId,
                'referring_realtor_id'     => $this->dealModel === 1 ? $this->referringRealtorId : null,
                'agent_id'                 => $this->agent->id,
                'agency_id'                => $agencyId,
                'total_amount'             => $totalAmount,
                'listing_realtor_amount'   => $splits['listing_realtor_amount'],
                'referring_realtor_amount' => $splits['referring_realtor_amount'],
                'agent_fee'                => $splits['agent_fee'],
                'platform_fee'             => $netPlatformFee,
                'agency_override_amount'   => $agencyOverride,
                'status'                   => 'pending',
                'deal_reference'           => $reference,
                'deal_date'                => now()->toDateString(),
            ]);

            $this->notifyDealLogged($commission->fresh(['property', 'listingRealtor', 'referringRealtor']));

            $this->closeLogDeal();
            $this->dispatch('agent-notify', message: "Deal logged as {$reference}. Commission split recorded.", type: 'success');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error("Log deal failed for inquiry #{$this->logDealId}: " . $e->getMessage());
            $this->dispatch('agent-notify', message: 'Failed to log deal.', type: 'error');
        }
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Fetch inquiry scoped to this agent's LGA, always with full relationship chain loaded.
     */
    private function findInArea(int $id): PropertyInquiry
    {
        return PropertyInquiry::with(['property', 'property.user', 'booker'])
            ->whereHas('property.user', fn ($q) => $q->where('Lga', $this->agent->Assigned_area))
            ->findOrFail($id);
    }

    /**
     * Send email to both the client and the realtor on every status change.
     * Each send is wrapped independently so one failure doesn't block the other.
     */
    private function notifyParties(PropertyInquiry $apt, string $type, ?string $reason = null): void
    {
        $sentCount = 0;

        // Notify client (prefer inline email, fall back to booker account)
        $clientEmail = $apt->email ?? $apt->booker?->Email;
        $clientName  = $apt->name ?: $apt->booker?->First_name;
        if ($clientEmail) {
            try {
                Mail::to($clientEmail)->send(new AppointmentStatusMail($apt, $type, $reason, $clientName));
                Log::info("AppointmentStatusMail [{$type}] sent to client {$clientEmail} for inquiry #{$apt->id}");
                $sentCount++;
            } catch (\Throwable $e) {
                Log::error("Client notification [{$type}] failed for inquiry #{$apt->id}: " . $e->getMessage());
            }
        } else {
            Log::warning("Client notification skipped — no email on inquiry #{$apt->id}");
        }

        // Notify realtor who owns the property
        $realtorEmail = $apt->property?->user?->Email;
        $realtorName  = trim("{$apt->property?->user?->First_name} {$apt->property?->user?->Last_name}") ?: null;
        if ($realtorEmail) {
            try {
                Mail::to($realtorEmail)->send(new AppointmentStatusMail($apt, $type, $reason, $realtorName));
                Log::info("AppointmentStatusMail [{$type}] sent to realtor {$realtorEmail} for inquiry #{$apt->id}");
                $sentCount++;
            } catch (\Throwable $e) {
                Log::error("Realtor notification [{$type}] failed for inquiry #{$apt->id}: " . $e->getMessage());
            }
        } else {
            Log::warning("Realtor notification skipped — no email on property for inquiry #{$apt->id}");
        }

        Log::info("notifyParties [{$type}] for inquiry #{$apt->id}: {$sentCount}/2 emails sent.");
    }

    /**
     * Email each realtor attributed to a newly logged deal about their commission share.
     * Independent try/catch per recipient so one failure doesn't block the other.
     */
    private function notifyDealLogged(CommissionTable $commission): void
    {
        $listingRealtor = $commission->listingRealtor;
        if ($listingRealtor?->Email) {
            try {
                Mail::to($listingRealtor->Email)->send(new \App\Mail\Agent\DealLoggedMail(
                    $commission,
                    trim("{$listingRealtor->First_name} {$listingRealtor->Last_name}"),
                    'listing',
                    (float) $commission->listing_realtor_amount,
                ));
                Log::info("DealLoggedMail sent to listing realtor {$listingRealtor->Email} for deal {$commission->deal_reference}");
            } catch (\Throwable $e) {
                Log::error("DealLoggedMail to listing realtor failed for deal {$commission->deal_reference}: " . $e->getMessage());
            }
        }

        $referringRealtor = $commission->referringRealtor;
        if ($referringRealtor?->Email) {
            try {
                Mail::to($referringRealtor->Email)->send(new \App\Mail\Agent\DealLoggedMail(
                    $commission,
                    trim("{$referringRealtor->First_name} {$referringRealtor->Last_name}"),
                    'referring',
                    (float) $commission->referring_realtor_amount,
                ));
                Log::info("DealLoggedMail sent to referring realtor {$referringRealtor->Email} for deal {$commission->deal_reference}");
            } catch (\Throwable $e) {
                Log::error("DealLoggedMail to referring realtor failed for deal {$commission->deal_reference}: " . $e->getMessage());
            }
        }

        // Notify agency if an override was routed to them
        if ($commission->agency_id && $commission->agency_override_amount > 0) {
            $agency = $commission->agency;
            if ($agency?->email) {
                try {
                    Mail::to($agency->email)->send(new \App\Mail\Agency\DealOverrideMail($commission, $agency));
                    Log::info("DealOverrideMail sent to agency {$agency->email} for deal {$commission->deal_reference}");
                } catch (\Throwable $e) {
                    Log::error("DealOverrideMail failed for deal {$commission->deal_reference}: " . $e->getMessage());
                }
            }
        }
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        $query = PropertyInquiry::with(['property', 'property.user', 'booker'])
            ->whereHas('property.user', fn ($q) => $q->where('Lga', $this->agent->Assigned_area));

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $appointments = $query->orderBy('preferred_date')->paginate(15);

        $viewing = $this->viewingId
            ? PropertyInquiry::with(['property', 'property.user', 'booker'])->find($this->viewingId)
            : null;

        $loggedPropertyIds = CommissionTable::pluck('property_id')->all();

        $splitSettings = CommissionSetting::current();

        return view('livewire.agent.appointments', compact('appointments', 'viewing', 'loggedPropertyIds', 'splitSettings'));
    }
}
