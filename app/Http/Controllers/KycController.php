<?php

namespace App\Http\Controllers;

use App\Models\KycVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KycController extends Controller
{
    public function show()
    {
        $kyc = KycVerification::where('user_id', Auth::id())->latest()->first();
        return view('user.kyc', compact('kyc'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name'  => 'required|string|max:255',
            'id_number'  => 'required|string|max:100',
            'id_front'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'id_back'    => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'address'    => 'required|string|max:500',
        ]);

        $uid = Auth::id();

        // Only allow submission if no pending/approved KYC
        $existing = KycVerification::where('user_id', $uid)
            ->whereIn('status', ['pending', 'approved'])->first();

        if ($existing) {
            return back()->with('error', 'You already have a KYC submission under review.');
        }

        $frontPath = $request->file('id_front')->store('kyc/front', 'public');
        $backPath  = $request->file('id_back')->store('kyc/back', 'public');

        KycVerification::create([
            'user_id'      => $uid,
            'full_name'    => $request->full_name,
            'id_number'    => $request->id_number,
            'id_front_path'=> $frontPath,
            'id_back_path' => $backPath,
            'address'      => $request->address,
            'status'       => 'pending',
        ]);

        return redirect()->route('user.dashboard')->with('success', 'KYC submitted successfully! We will review it shortly.');
    }
}
