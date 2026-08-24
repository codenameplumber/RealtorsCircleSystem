<?php

namespace App\Livewire\Admin;

use App\Models\AdminAuditLog;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLog extends Component
{
    use WithPagination;

    public string $search       = '';
    public string $actionFilter = '';

    public function updatingSearch()       { $this->resetPage(); }
    public function updatingActionFilter() { $this->resetPage(); }

    public function render()
    {
        $logs = AdminAuditLog::query()
            ->when($this->search, fn ($q) => $q->where(fn ($q2) =>
                $q2->where('admin_name', 'like', "%{$this->search}%")
                   ->orWhere('description', 'like', "%{$this->search}%")
            ))
            ->when($this->actionFilter, fn ($q) => $q->where('action', $this->actionFilter))
            ->latest('created_at')
            ->paginate(20);

        $actions = AdminAuditLog::query()->distinct()->orderBy('action')->pluck('action');

        return view('livewire.admin.audit-log', [
            'logs'    => $logs,
            'actions' => $actions,
        ]);
    }
}
