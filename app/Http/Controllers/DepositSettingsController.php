<?php
namespace App\Http\Controllers;

use App\Models\DepositSetting;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DepositSettingsController extends Controller
{
    public function edit()
    {
        $setting = DepositSetting::where('coin','USDT')->first();
        return view('admin.deposit-settings', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'wallet_address' => 'required|string|max:255',
            'account_name'   => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'bank_name'      => 'nullable|string|max:255',
            'network'        => 'nullable|string|max:100',
            'instructions'   => 'nullable|string|max:2000',
            'qr_image'       => 'nullable|file|image|max:2048',
        ]);

        $setting = DepositSetting::firstOrNew(['coin' => 'USDT']);

        // Handle QR image removal
        if ($request->boolean('remove_qr') && $setting->qr_image_path) {
            Storage::disk('public')->delete($setting->qr_image_path);
            $setting->qr_image_path = null;
        }

        // Handle QR image upload
        if ($request->hasFile('qr_image')) {
            if ($setting->qr_image_path) {
                Storage::disk('public')->delete($setting->qr_image_path);
            }
            $setting->qr_image_path = $request->file('qr_image')->store('deposit-settings/qr', 'public');
        }

        $setting->fill([
            'account_name'   => $request->account_name,
            'account_number' => $request->account_number,
            'bank_name'      => $request->bank_name,
            'network'        => $request->network,
            'wallet_address' => $request->wallet_address,
            'instructions'   => $request->instructions,
            'is_active'      => true,
        ])->save();

        return back()->with('success', 'USDT deposit settings saved successfully.');
    }
}
