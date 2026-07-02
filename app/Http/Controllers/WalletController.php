<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    // Display add wallet form
    public function addWallet()
    {
        return view('admin.wallets.add-wallet');
    }

    // Store new wallet
    public function storeWallet(Request $request)
    {
        // Validate inputs
        $validator = Validator::make($request->all(), [
            'crypto_name' => 'required|string|max:255',
            'wallet_address' => 'required|string|unique:wallets,wallet_address|max:255',
        ], [
            'crypto_name.required' => 'Crypto name is required.',
            'wallet_address.required' => 'Wallet address is required.',
            'wallet_address.unique' => 'This wallet address is already registered.',
        ]);

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create wallet
        Wallet::create([
            'crypto_name' => $request->crypto_name,
            'wallet_address' => $request->wallet_address,
        ]);

        return redirect()->route('admin.wallets.index')
            ->with('success', 'Wallet address saved successfully.');
    }

    // Display list of all wallets
    public function index()
    {
        $wallets = Wallet::paginate(10);
        return view('admin.wallets.wallet-list', compact('wallets'));
    }

    // Show edit form for a specific wallet
    public function edit($id)
    {
        $wallet = Wallet::findOrFail($id);
        return view('admin.wallets.editwallet', compact('wallet'));
    }

    // Update a specific wallet
    public function update(Request $request, $id)
    {
        $wallet = Wallet::findOrFail($id);

        // Validate inputs
        $validator = Validator::make($request->all(), [
            'crypto_name' => 'required|string|max:255',
            'wallet_address' => 'required|string|max:255|unique:wallets,wallet_address,' . $id,
        ], [
            'crypto_name.required' => 'Crypto name is required.',
            'wallet_address.required' => 'Wallet address is required.',
            'wallet_address.unique' => 'This wallet address is already registered.',
        ]);

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update wallet
        $wallet->update([
            'crypto_name' => $request->crypto_name,
            'wallet_address' => $request->wallet_address,
        ]);

        return redirect()->route('admin.wallets.index')
            ->with('success', 'Wallet updated successfully.');
    }

    // Delete a wallet
    public function destroy($id)
    {
        $wallet = Wallet::findOrFail($id);
        $wallet->delete();

        return redirect()->back()->with('success', 'Wallet deleted successfully.');
    }

    // Generate wallets for API
    public function generate(Request $request)
    {
        // Simply return all wallets from the database
        $wallets = Wallet::all();
        
        Log::info('Wallets fetched', [
            'user_id' => auth()->id(),
            'count' => $wallets->count(),
            'ip' => $request->ip()
        ]);
        
        return response()->json([
            'success' => true,
            'wallets' => $wallets
        ]);
    }
}