<?php

namespace App\Livewire\Admin;

use App\Models\AdminAuditLog;
use App\Models\CommissionSetting;
use Livewire\Component;

class CommissionSettings extends Component
{
    public int $m1_listing_pct   = 40;
    public int $m1_referring_pct = 40;
    public int $m1_agent_pct     = 10;
    public int $m1_platform_pct  = 10;

    public int $m2_listing_pct  = 60;
    public int $m2_agent_pct    = 20;
    public int $m2_platform_pct = 20;

    public int $agency_override_pct = 20;

    public function mount(): void
    {
        $settings = CommissionSetting::current();

        $this->m1_listing_pct   = $settings->m1_listing_pct;
        $this->m1_referring_pct = $settings->m1_referring_pct;
        $this->m1_agent_pct     = $settings->m1_agent_pct;
        $this->m1_platform_pct  = $settings->m1_platform_pct;

        $this->m2_listing_pct  = $settings->m2_listing_pct;
        $this->m2_agent_pct    = $settings->m2_agent_pct;
        $this->m2_platform_pct = $settings->m2_platform_pct;

        $this->agency_override_pct = $settings->agency_override_pct ?? 20;
    }

    public function m1Total(): int
    {
        return $this->m1_listing_pct + $this->m1_referring_pct + $this->m1_agent_pct + $this->m1_platform_pct;
    }

    public function m2Total(): int
    {
        return $this->m2_listing_pct + $this->m2_agent_pct + $this->m2_platform_pct;
    }

    public function save(): void
    {
        $this->validate([
            'm1_listing_pct'    => ['required', 'integer', 'min:0', 'max:100'],
            'm1_referring_pct'  => ['required', 'integer', 'min:0', 'max:100'],
            'm1_agent_pct'      => ['required', 'integer', 'min:0', 'max:100'],
            'm1_platform_pct'   => ['required', 'integer', 'min:0', 'max:100'],
            'm2_listing_pct'    => ['required', 'integer', 'min:0', 'max:100'],
            'm2_agent_pct'      => ['required', 'integer', 'min:0', 'max:100'],
            'm2_platform_pct'   => ['required', 'integer', 'min:0', 'max:100'],
            'agency_override_pct' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        if ($this->m1Total() !== 100) {
            $this->addError('m1_listing_pct', 'Model 1 percentages must add up to exactly 100%.');
            return;
        }

        if ($this->m2Total() !== 100) {
            $this->addError('m2_listing_pct', 'Model 2 percentages must add up to exactly 100%.');
            return;
        }

        CommissionSetting::current()->update([
            'm1_listing_pct'    => $this->m1_listing_pct,
            'm1_referring_pct'  => $this->m1_referring_pct,
            'm1_agent_pct'      => $this->m1_agent_pct,
            'm1_platform_pct'   => $this->m1_platform_pct,
            'm2_listing_pct'    => $this->m2_listing_pct,
            'm2_agent_pct'      => $this->m2_agent_pct,
            'm2_platform_pct'   => $this->m2_platform_pct,
            'agency_override_pct' => $this->agency_override_pct,
        ]);

        AdminAuditLog::record(
            'commission_settings.update',
            'CommissionSetting',
            null,
            "M1: {$this->m1_listing_pct}/{$this->m1_referring_pct}/{$this->m1_agent_pct}/{$this->m1_platform_pct}, "
                . "M2: {$this->m2_listing_pct}/{$this->m2_agent_pct}/{$this->m2_platform_pct}, "
                . "Agency override: {$this->agency_override_pct}%"
        );

        session()->flash('commission_settings_success', 'Commission split settings saved successfully.');
    }

    public function render()
    {
        return view('livewire.admin.commission-settings');
    }
}
