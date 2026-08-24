<?php

namespace App\Livewire;

use App\Data\NigeriaLocations;
use App\Models\PropertiesTable;
use App\Models\PropertyInquiry;
use App\Models\RealtorRating;
use App\Models\UsersTable;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class RealtorSearch extends Component
{
    use WithPagination;

    public string $state          = '';
    public string $lga            = '';
    public string $specialization = '';
    public string $sortBy         = 'newest';
    public int    $perPage        = 12;

    // Rating submission
    public int    $ratingRealtorId = 0;
    public int    $ratingValue     = 0;
    public bool   $ratingSubmitted = false;
    public string $ratingMessage   = '';

    protected $queryString = [
        'state'          => ['except' => ''],
        'lga'            => ['except' => ''],
        'specialization' => ['except' => ''],
        'sortBy'         => ['except' => 'newest'],
    ];

    public function updatingState() { $this->resetPage(); $this->lga = ''; }
    public function updatingLga()   { $this->resetPage(); }

    public function clearFilters(): void
    {
        $this->reset(['state', 'lga', 'specialization', 'sortBy']);
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

    public function submitRating(int $realtorId, int $stars): void
    {
        if ($stars < 1 || $stars > 5) return;

        $token = session()->getId() . '_' . $realtorId;

        $exists = RealtorRating::where('realtor_id', $realtorId)
            ->where('session_token', $token)
            ->exists();

        if ($exists) {
            $this->ratingMessage   = 'You have already rated this realtor.';
            $this->ratingRealtorId = $realtorId;
            return;
        }

        RealtorRating::create([
            'realtor_id'    => $realtorId,
            'rating'        => $stars,
            'session_token' => $token,
        ]);

        $this->ratingRealtorId = $realtorId;
        $this->ratingSubmitted = true;
        $this->ratingMessage   = 'Thanks for your rating!';
    }

    public function render()
    {
        $query = UsersTable::query()
            ->where('Status', 'active')
            ->withCount(['properties as total_assets'])
            ->addSelect('users_tables.*')
            ->addSelect(DB::raw('(SELECT COALESCE(SUM(views),0) FROM properties_tables WHERE user_id = users_tables.id) as total_views'))
            ->addSelect(DB::raw('(SELECT COUNT(*) FROM property_inquiries pi JOIN properties_tables pt ON pt.id = pi.property_id WHERE pt.user_id = users_tables.id) as total_appointments'))
            ->addSelect(DB::raw('(SELECT ROUND(AVG(rating),1) FROM realtor_ratings WHERE realtor_id = users_tables.id) as avg_rating'))
            ->addSelect(DB::raw('(SELECT COUNT(*) FROM realtor_ratings WHERE realtor_id = users_tables.id) as rating_count'));

        if ($this->state)          $query->where('State', $this->state);
        if ($this->lga)            $query->where('Lga', $this->lga);
        if ($this->specialization) $query->where('Specialization', $this->specialization);

        $query = match ($this->sortBy) {
            'most_assets'   => $query->orderByDesc('total_assets'),
            'most_viewed'   => $query->orderByDesc('total_views'),
            'highest_rated' => $query->orderByDesc('avg_rating'),
            default         => $query->latest(),
        };

        $realtors = $query->paginate($this->perPage);

        // Load newest 10 listings per realtor (keyed by realtor id)
        $realtorIds = $realtors->pluck('id');
        $listings = PropertiesTable::whereIn('user_id', $realtorIds)
            ->where('approval_status', 'approved')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('user_id')
            ->map(fn($group) => $group->take(10));

        return view('livewire.realtor-search', compact('realtors', 'listings'));
    }
}
