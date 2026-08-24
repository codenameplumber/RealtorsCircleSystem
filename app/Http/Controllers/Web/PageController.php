<?php

namespace App\Http\Controllers\Web;

use App\Helpers\IdHasher;
use App\Http\Controllers\Controller;
use App\Models\PropertiesTable;
use App\Models\PropertyInquiry;
use App\Models\RealtorRating;
use App\Models\UsersTable;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        $featuredProperties = PropertiesTable::where('approval_status', 'approved')
            ->where('listing_status', 'active')
            ->latest()
            ->take(6)
            ->get();

        $stats = [
            'properties' => PropertiesTable::where('approval_status', 'approved')->count(),
            'realtors'   => UsersTable::where('Status', 'active')->count(),
            'cities'     => PropertiesTable::distinct('city')->count('city'),
        ];

        return view('pages.home', compact('featuredProperties', 'stats'));
    }

    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function properties()
    {
        return view('pages.properties');
    }

    public function propertyDetail(string $hash)
    {
        $id = IdHasher::decode($hash);
        abort_if(! $id, 404);

        $property = PropertiesTable::with('user')
            ->where('approval_status', 'approved')
            ->where('listing_status', 'active')
            ->findOrFail($id);

        // Increment views
        $property->increment('views');

        $related = PropertiesTable::where('type', $property->type)
            ->where('id', '!=', $id)
            ->where('approval_status', 'approved')
            ->where('listing_status', 'active')
            ->take(3)
            ->get();

        return view('pages.property-detail', compact('property', 'related'));
    }

    public function realtors()
    {
        $totalRealtors = UsersTable::where('Status', 'active')->count();
        return view('pages.realtors', compact('totalRealtors'));
    }

    public function realtorDetail(string $hash)
    {
        $id = IdHasher::decode($hash);
        abort_if(! $id, 404);

        $realtor = UsersTable::where('Status', 'active')->findOrFail($id);

        $stats = [
            'total_assets'       => PropertiesTable::where('user_id', $id)->count(),
            'total_views'        => PropertiesTable::where('user_id', $id)->sum('views'),
            'total_appointments' => PropertyInquiry::whereHas('property', fn($q) => $q->where('user_id', $id))->count(),
            'closed_deals'       => PropertyInquiry::whereHas('property', fn($q) => $q->where('user_id', $id))->where('status', 'completed')->count(),
            'avg_rating'         => round(RealtorRating::where('realtor_id', $id)->avg('rating') ?? 0, 1),
            'rating_count'       => RealtorRating::where('realtor_id', $id)->count(),
        ];

        $listings = PropertiesTable::where('user_id', $id)
            ->where('approval_status', 'approved')
            ->where('listing_status', 'active')
            ->latest()
            ->paginate(9);

        $closedDeals = PropertyInquiry::whereHas('property', fn($q) => $q->where('user_id', $id))
            ->with('property')
            ->where('status', 'completed')
            ->latest()
            ->take(6)
            ->get();

        return view('pages.realtor-detail', compact('realtor', 'stats', 'listings', 'closedDeals', 'hash'));
    }

    public function terms()
    {
        return view('pages.terms');
    }

    public function privacy()
    {
        return view('pages.privacy');
    }
}
