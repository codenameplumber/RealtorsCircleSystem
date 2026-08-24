<?php

namespace App\Livewire;

use App\Data\NigeriaLocations;
use App\Models\PropertiesTable;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class PropertySearch extends Component
{
    use WithPagination;

    public string $search      = '';
    public string $type        = '';
    public string $listingType = '';
    public string $state       = '';
    public string $lga         = '';
    public string $sortBy      = 'latest';
    public int    $perPage     = 9;

    protected $queryString = [
        'search'      => ['except' => ''],
        'type'        => ['except' => ''],
        'listingType' => ['except' => ''],
        'state'       => ['except' => ''],
        'lga'         => ['except' => ''],
        'sortBy'      => ['except' => 'latest'],
    ];

    public function updatingSearch()     { $this->resetPage(); }
    public function updatingType()       { $this->resetPage(); }
    public function updatingListingType(){ $this->resetPage(); }
    public function updatingState()      { $this->resetPage(); $this->lga = ''; }
    public function updatingLga()        { $this->resetPage(); }
    public function updatingSortBy()     { $this->resetPage(); }

    public function clearFilters(): void
    {
        $this->reset(['search', 'type', 'listingType', 'state', 'lga', 'sortBy']);
        $this->resetPage();
    }

    #[Computed]
    public function states(): array
    {
        return NigeriaLocations::states();
    }

    #[Computed]
    public function lgas(): array
    {
        return $this->state ? NigeriaLocations::lgasFor($this->state) : [];
    }

    private function buildQuery()
    {
        $query = PropertiesTable::query()
            ->where('approval_status', 'approved')
            ->where('listing_status', 'active');

        if ($this->type)        $query->where('type', $this->type);
        if ($this->listingType) $query->where('listing_type', $this->listingType);
        if ($this->state)       $query->where('state', $this->state);
        if ($this->lga)         $query->where('localarea', $this->lga);

        if ($this->search) {
            $term = $this->search;
            $query->where(function ($q) use ($term) {
                $q->where('title',        'like', "%{$term}%")
                  ->orWhere('state',       'like', "%{$term}%")
                  ->orWhere('city',        'like', "%{$term}%")
                  ->orWhere('localarea',   'like', "%{$term}%")
                  ->orWhere('neigborhood', 'like', "%{$term}%")
                  ->orWhere('street',      'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");
            });

            if ($this->sortBy === 'latest') {
                $escaped = DB::connection()->getPdo()->quote("%{$term}%");
                return $query->orderByRaw("
                    (CASE WHEN state       LIKE {$escaped} THEN 10 ELSE 0 END +
                     CASE WHEN city        LIKE {$escaped} THEN  9 ELSE 0 END +
                     CASE WHEN localarea   LIKE {$escaped} THEN  8 ELSE 0 END +
                     CASE WHEN neigborhood LIKE {$escaped} THEN  7 ELSE 0 END +
                     CASE WHEN street      LIKE {$escaped} THEN  6 ELSE 0 END +
                     CASE WHEN title       LIKE {$escaped} THEN  5 ELSE 0 END +
                     CASE WHEN description LIKE {$escaped} THEN  2 ELSE 0 END
                    ) DESC, created_at DESC
                ");
            }
        }

        return match ($this->sortBy) {
            'price_asc'  => $query->orderByRaw("CAST(REPLACE(REPLACE(price, ',', ''), '/yr', '') AS UNSIGNED) ASC"),
            'price_desc' => $query->orderByRaw("CAST(REPLACE(REPLACE(price, ',', ''), '/yr', '') AS UNSIGNED) DESC"),
            'views'      => $query->orderBy('views', 'desc'),
            default      => $query->latest(),
        };
    }

    public function render()
    {
        $properties = $this->buildQuery()->paginate($this->perPage);
        $total      = $properties->total();

        return view('livewire.property-search', compact('properties', 'total'));
    }
}
