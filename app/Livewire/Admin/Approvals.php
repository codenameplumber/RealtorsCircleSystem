<?php

namespace App\Livewire\Admin;

use App\Mail\Agency\AgencyApprovedMail;
use App\Mail\Agent\PropertyApprovedMail;
use App\Mail\Agent\PropertyRejectedMail;
use App\Mail\Agent\RealtorApprovedMail;
use App\Mail\Agent\RealtorRejectedMail;
use App\Models\AdminAuditLog;
use App\Models\AgencyPartner;
use App\Models\PropertiesTable;
use App\Models\UsersTable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Approvals extends Component
{
    public bool   $rejectOpen   = false;
    public ?int   $rejectId     = null;
    public string $rejectType   = '';
    public string $rejectReason = '';

    public bool $detailOpen = false;
    public ?int $viewingId  = null;

    public function viewRealtor(int $id): void
    {
        $this->viewingId  = $id;
        $this->detailOpen = true;
    }

    public function closeDetail(): void
    {
        $this->detailOpen = false;
        $this->viewingId  = null;
    }

    public function approveRealtor(int $id): void
    {
        try {
            $realtor = UsersTable::findOrFail($id);
            $realtor->update(['Status' => 'active']);

            try {
                Mail::to($realtor->Email)->send(new RealtorApprovedMail($realtor));
            } catch (\Throwable $e) {
                Log::error("RealtorApprovedMail failed for realtor #{$id}: " . $e->getMessage());
            }

            AdminAuditLog::record('realtor.approve', 'UsersTable', $id, "Approved realtor {$realtor->First_name} {$realtor->Last_name}");

            $this->closeDetail();
            $this->dispatch('admin-notify', message: 'Realtor approved. Notification sent.', type: 'success');
        } catch (\Throwable $e) {
            Log::error("Admin realtor approve failed #{$id}: " . $e->getMessage());
            $this->dispatch('admin-notify', message: 'Failed to approve realtor. Please try again.', type: 'error');
        }
    }

    public function approveProperty(int $id): void
    {
        try {
            $property = PropertiesTable::with('user')->findOrFail($id);
            $property->update(['approval_status' => 'approved']);

            if ($property->user) {
                try {
                    Mail::to($property->user->Email)->send(new PropertyApprovedMail($property));
                } catch (\Throwable $e) {
                    Log::error("PropertyApprovedMail failed for property #{$id}: " . $e->getMessage());
                }
            }

            AdminAuditLog::record('property.approve', 'PropertiesTable', $id, "Approved property \"{$property->title}\"");

            $this->dispatch('admin-notify', message: 'Property approved. Realtor notified.', type: 'success');
        } catch (\Throwable $e) {
            Log::error("Admin property approve failed #{$id}: " . $e->getMessage());
            $this->dispatch('admin-notify', message: 'Failed to approve property. Please try again.', type: 'error');
        }
    }

    public function approveAgency(int $id): void
    {
        $agency = AgencyPartner::findOrFail($id);
        $agency->update(['status' => 'active']);

        try {
            Mail::to($agency->email)->send(new AgencyApprovedMail($agency));
        } catch (\Throwable $e) {
            Log::error("AgencyApprovedMail failed for agency #{$id}: " . $e->getMessage());
        }

        AdminAuditLog::record('agency.approve', 'AgencyPartner', $id, "Approved agency \"{$agency->company_name}\"");

        $this->dispatch('admin-notify', message: 'Agency approved. Notification sent.', type: 'success');
    }

    // ── Reject (shared modal for realtor / property) ────────────────────────────

    public function openReject(int $id, string $type): void
    {
        $this->rejectId     = $id;
        $this->rejectType   = $type;
        $this->rejectReason = '';
        $this->resetErrorBag();
        $this->rejectOpen   = true;
    }

    public function closeReject(): void
    {
        $this->rejectOpen   = false;
        $this->rejectId     = null;
        $this->rejectType   = '';
        $this->rejectReason = '';
    }

    public function submitReject(): void
    {
        $this->validate(
            ['rejectReason' => 'required|string|min:10|max:500'],
            [
                'rejectReason.required' => 'Please provide a reason for rejection.',
                'rejectReason.min'      => 'Reason must be at least 10 characters.',
            ],
        );

        try {
            if ($this->rejectType === 'realtor') {
                $realtor = UsersTable::findOrFail($this->rejectId);
                $realtor->update(['Status' => 'suspended']);

                try {
                    Mail::to($realtor->Email)->send(new RealtorRejectedMail($realtor, $this->rejectReason));
                } catch (\Throwable $e) {
                    Log::error("RealtorRejectedMail failed for realtor #{$this->rejectId}: " . $e->getMessage());
                }

                AdminAuditLog::record('realtor.reject', 'UsersTable', $this->rejectId, $this->rejectReason);

                $this->closeDetail();
                $this->dispatch('admin-notify', message: 'Realtor rejected. Notification sent.', type: 'success');
            } elseif ($this->rejectType === 'property') {
                $property = PropertiesTable::with('user')->findOrFail($this->rejectId);
                $property->update(['approval_status' => 'rejected']);

                if ($property->user) {
                    try {
                        Mail::to($property->user->Email)->send(new PropertyRejectedMail($property, $this->rejectReason));
                    } catch (\Throwable $e) {
                        Log::error("PropertyRejectedMail failed for property #{$this->rejectId}: " . $e->getMessage());
                    }
                }

                AdminAuditLog::record('property.reject', 'PropertiesTable', $this->rejectId, $this->rejectReason);

                $this->dispatch('admin-notify', message: 'Property rejected. Realtor notified.', type: 'success');
            } elseif ($this->rejectType === 'agency') {
                AgencyPartner::findOrFail($this->rejectId)->update(['status' => 'suspended']);

                AdminAuditLog::record('agency.reject', 'AgencyPartner', $this->rejectId, $this->rejectReason);

                $this->dispatch('admin-notify', message: 'Agency application rejected.', type: 'success');
            }

            $this->closeReject();
        } catch (\Throwable $e) {
            Log::error("Admin reject failed [{$this->rejectType}] #{$this->rejectId}: " . $e->getMessage());
            $this->dispatch('admin-notify', message: 'Failed to reject. Please try again.', type: 'error');
        }
    }

    public function render()
    {
        $viewingRealtor = $this->viewingId
            ? UsersTable::with('bankInformation')->find($this->viewingId)
            : null;

        return view('livewire.admin.approvals', [
            'pendingRealtors'   => UsersTable::where('Status', 'pending')->latest()->get(),
            'pendingProperties' => PropertiesTable::where('approval_status', 'pending')->with('user')->latest()->get(),
            'pendingAgencies'   => AgencyPartner::where('status', 'pending')->latest()->get(),
            'viewingRealtor'    => $viewingRealtor,
        ]);
    }
}
