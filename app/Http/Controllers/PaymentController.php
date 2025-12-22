<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PaymentController extends Controller
{
    public function checkout()
    {
        $user = Auth::user();

        if ($user->isPremium()) {
            return redirect()->route('dashboard')->with('error', __('You are already a Premium member.'));
        }

        return view('payment.checkout', compact('user'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:credit_card,bank_transfer,ewallet',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->account_type = 'PREMIUM'; 
        $user->premium_expires_at = now()->addYears(1); 
        
        $user->save();
        return redirect()->route('dashboard')->with('success', __('Payment successful! You are now a Premium member.'));
    }
}