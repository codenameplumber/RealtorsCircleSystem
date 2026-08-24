<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Helpers\IdHasher;
use App\Http\Controllers\Controller;
use App\Models\CommissionTable;
use App\Models\PropertiesTable;
use App\Models\PropertyInquiry;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class RealtorDashboardController extends Controller
{
    private function user() { return auth()->user(); }

    private function commissionColumnsExist(): bool
    {
        try {
            return Schema::hasColumn('commission_tables', 'listing_realtor_amount');
        } catch (\Throwable) {
            return false;
        }
    }

    public function index()
    {
        $userId = $this->user()->id;

        $totalCommission = 0;
        $monthlyCommission = array_fill(0, 12, 0);

        if ($this->commissionColumnsExist()) {
            try {
                $totalCommission = CommissionTable::where('listing_realtor_id', $userId)
                    ->orWhere('referring_realtor_id', $userId)
                    ->where('status', 'paid')
                    ->sum('listing_realtor_amount');

                $monthlyCommission = $this->getMonthlyCommission($userId);
            } catch (\Throwable) {}
        }

        $activeListings = PropertiesTable::where('user_id', $userId)
            ->where('listing_status', 'active')
            ->count();

        $newLeads = 0;
        $upcomingViewings = 0;
        $recentLeads = collect();

        try {
            $newLeads = PropertyInquiry::whereHas('property', fn($q) => $q->where('user_id', $userId))
                ->where('status', 'pending')
                ->count();

            $upcomingViewings = PropertyInquiry::whereHas('property', fn($q) => $q->where('user_id', $userId))
                ->where('preferred_date', '>=', now())
                ->where('status', 'confirmed')
                ->count();

            $recentLeads = PropertyInquiry::whereHas('property', fn($q) => $q->where('user_id', $userId))
                ->latest()
                ->take(5)
                ->get();
        } catch (\Throwable) {}

        $myProperties = PropertiesTable::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.realtor.index', compact(
            'totalCommission', 'activeListings', 'newLeads',
            'upcomingViewings', 'recentLeads', 'myProperties', 'monthlyCommission'
        ));
    }

    public function properties()
    {
        $properties = PropertiesTable::where('user_id', $this->user()->id)->latest()->paginate(10);
        return view('dashboard.realtor.properties', compact('properties'));
    }

    public function leads()
    {
        try {
            $leads = PropertyInquiry::whereHas('property', fn($q) => $q->where('user_id', $this->user()->id))
                ->with('property')
                ->latest()
                ->paginate(15);
        } catch (\Throwable) {
            $leads = new LengthAwarePaginator([], 0, 15);
        }
        return view('dashboard.realtor.leads', compact('leads'));
    }

    public function appointments()
    {
        try {
            $appointments = PropertyInquiry::whereHas('property', fn($q) => $q->where('user_id', $this->user()->id))
                ->with('property')
                ->whereNotIn('status', ['completed'])
                ->orderBy('preferred_date')
                ->paginate(15);
        } catch (\Throwable) {
            $appointments = new LengthAwarePaginator([], 0, 15);
        }
        return view('dashboard.realtor.appointments', compact('appointments'));
    }

    public function commission()
    {
        return view('dashboard.realtor.commission');
    }

    public function payments()
    {
        return view('dashboard.realtor.payments');
    }

    public function showProperty(string $hash)
    {
        $id = IdHasher::decode($hash);
        abort_if(! $id, 404);

        $property = PropertiesTable::where('user_id', $this->user()->id)
            ->findOrFail($id);

        return view('dashboard.realtor.property-show', compact('property'));
    }

    public function editProperty(string $hash)
    {
        $id = IdHasher::decode($hash);
        abort_if(! $id, 404);

        // Verify ownership before rendering
        PropertiesTable::where('user_id', $this->user()->id)->findOrFail($id);

        return view('dashboard.realtor.property-form', ['propertyId' => $id]);
    }

    public function settings()
    {
        return view('dashboard.realtor.settings', ['user' => $this->user()]);
    }

    private function getMonthlyCommission(int $userId): array
    {
        $data = array_fill(0, 12, 0);
        try {
            $records = CommissionTable::where('listing_realtor_id', $userId)
                ->orWhere('referring_realtor_id', $userId)
                ->where('status', 'paid')
                ->whereYear('created_at', now()->year)
                ->selectRaw('MONTH(created_at) as month, SUM(listing_realtor_amount + referring_realtor_amount) as total')
                ->groupBy('month')
                ->get();

            foreach ($records as $r) {
                $data[$r->month - 1] = (float) $r->total;
            }
        } catch (\Throwable) {}

        return $data;
    }
}
