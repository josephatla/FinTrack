<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpgradeController extends Controller
{
    //
    public function instantUpgrade()
    {
        $user = Auth::user();

        if ($user->isPremium()) {
            return redirect()->route('dashboard')->with('info', 'You are already a Premium member!');
        }

        $user->account_type = 'PREMIUM';
        $user->premium_expires_at = now()->addYears(1); 
        $user->save();

        return redirect()->route('dashboard')->with('success', __('messages.upgrade_success'));
    }
}
