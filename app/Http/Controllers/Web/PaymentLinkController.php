<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CommissionTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentLinkController extends Controller
{
    public function show(string $token): View
    {
        $commission = CommissionTable::with(['property', 'listingRealtor', 'referringRealtor', 'agent'])
            ->where('payment_token', $token)
            ->firstOrFail();

        return view('pages.payment-link', compact('commission'));
    }

    public function process(string $token): RedirectResponse
    {
        $commission = CommissionTable::where('payment_token', $token)->firstOrFail();

        if ($commission->status === 'pending') {
            $commission->update(['status' => 'processing']);
            session()->flash('payment_message', 'Payment initiated — your transaction is now being processed. You will receive confirmation shortly.');
        } else {
            session()->flash('payment_message', 'This deal is already ' . $commission->status . '.');
        }

        return redirect()->route('payment.show', $token);
    }
}
