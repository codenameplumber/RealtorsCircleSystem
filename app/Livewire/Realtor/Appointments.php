<?php

namespace App\Livewire\Realtor;

use App\Mail\AppointmentStatusMail;
use App\Models\PropertyInquiry;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;

class Appointments extends Component
{
    use WithPagination;

    public bool   $rescheduleOpen = false;
    public bool   $cancelOpen     = false;
    public ?int   $activeId       = null;
    public string $rescheduleDate = '';
    public string $rescheduleTime = '';
    public string $cancelReason   = '';

    public function accept(int $id): void
    {
        $apt = $this->find($id);
        $apt->update(['status' => 'confirmed']);

        $sent = $this->sendMail($apt->fresh(['property', 'booker']), 'confirmed');

        $this->notify(
            $sent
                ? 'Appointment confirmed — notification email sent to client.'
                : 'Appointment confirmed. No client email on record, so no notification was sent.',
            $sent ? 'success' : 'warning'
        );
    }

    public function openReschedule(int $id): void
    {
        $this->activeId       = $id;
        $this->rescheduleDate = '';
        $this->rescheduleTime = '';
        $this->resetErrorBag();
        $this->rescheduleOpen = true;
    }

    public function submitReschedule(): void
    {
        $this->validate([
            'rescheduleDate' => 'required|date|after:today',
            'rescheduleTime' => 'required|string',
        ], [
            'rescheduleDate.required' => 'Please pick a new date.',
            'rescheduleDate.after'    => 'The new date must be in the future.',
            'rescheduleTime.required' => 'Please pick a new time.',
        ]);

        $apt = $this->find($this->activeId);
        $apt->update([
            'reschedule_date' => $this->rescheduleDate,
            'reschedule_time' => $this->rescheduleTime,
            'status'          => 'confirmed',
        ]);

        $sent = $this->sendMail($apt->fresh(['property', 'booker']), 'rescheduled');

        $this->rescheduleOpen = false;
        $this->notify(
            $sent
                ? 'Appointment rescheduled — notification email sent to client.'
                : 'Appointment rescheduled. No client email on record, so no notification was sent.',
            $sent ? 'success' : 'warning'
        );
    }

    public function openCancel(int $id): void
    {
        $this->activeId     = $id;
        $this->cancelReason = '';
        $this->resetErrorBag();
        $this->cancelOpen = true;
    }

    public function submitCancel(): void
    {
        $apt = $this->find($this->activeId);
        $apt->update([
            'status'        => 'cancelled',
            'cancel_reason' => $this->cancelReason ?: null,
        ]);

        $sent = $this->sendMail($apt->fresh(['property', 'booker']), 'cancelled', $this->cancelReason ?: null);

        $this->cancelOpen = false;
        $this->notify(
            $sent
                ? 'Appointment cancelled — notification email sent to client.'
                : 'Appointment cancelled. No client email on record, so no notification was sent.',
            $sent ? 'success' : 'warning'
        );
    }

    // ─── Helpers ────────────────────────────────────────────────────────────

    private function find(int $id): PropertyInquiry
    {
        return PropertyInquiry::whereHas('property', fn($q) => $q->where('user_id', auth()->id()))
            ->with(['property', 'booker'])
            ->findOrFail($id);
    }

    private function resolveEmail(PropertyInquiry $apt): ?string
    {
        if (!empty($apt->email)) {
            return $apt->email;
        }

        if ($apt->booker && !empty($apt->booker->Email)) {
            return $apt->booker->Email;
        }

        return null;
    }

    /** Returns true if the email was sent successfully, false otherwise. */
    private function sendMail(PropertyInquiry $apt, string $type, ?string $reason = null): bool
    {
        $to = $this->resolveEmail($apt);

        if (!$to) {
            Log::warning("AppointmentStatusMail skipped — no email address on inquiry #{$apt->id}");
            return false;
        }

        try {
            Mail::to($to)->send(new AppointmentStatusMail($apt, $type, $reason));
            Log::info("AppointmentStatusMail [{$type}] sent to {$to} for inquiry #{$apt->id}");
            return true;
        } catch (\Throwable $e) {
            Log::error("AppointmentStatusMail [{$type}] FAILED for inquiry #{$apt->id}: " . $e->getMessage());
            return false;
        }
    }

    private function notify(string $message, string $type = 'success'): void
    {
        $this->dispatch('apt-notify', message: $message, type: $type);
    }

    public function render()
    {
        $appointments = PropertyInquiry::whereHas('property', fn($q) => $q->where('user_id', auth()->id()))
            ->with(['property', 'booker'])
            ->whereNotIn('status', ['completed'])
            ->orderBy('preferred_date')
            ->paginate(15);

        return view('livewire.realtor.appointments', compact('appointments'));
    }
}
