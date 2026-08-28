<?php

namespace App\Http\Controllers;

use App\Mail\AdminBankTransferNotification;
use App\Mail\AdminBankTransferProofSubmitted;
use App\Mail\BankTransferDetailsMail;
use App\Models\BankTransfer;
use App\Models\User;
use App\Models\Deposit;
use App\Models\Plan;
use App\Models\Wallet;
use App\Notifications\TransactionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class DepositController extends Controller
{
    // ─────────────────────────────────────────
    // Show deposit form
    // ─────────────────────────────────────────
    public function userDeposit()
    {
        $wallets          = Wallet::all();
        $reinvestmentMode = $this->checkReinvestmentMode();

        return view('dashboard.deposit.create-deposit', compact('wallets', 'reinvestmentMode'));
    }

    // ─────────────────────────────────────────
    // Handle crypto deposit → confirm page
    // ─────────────────────────────────────────
    public function userMakeDeposit(Request $request)
    {
        $request->validate([
            'wallet_id'  => 'required|exists:wallets,id',
            'amount'     => 'required|numeric|min:0.01',
            'amount_usd' => 'required|numeric|min:0.01',
        ]);

        $user      = auth()->user();
        $wallet    = Wallet::findOrFail($request->wallet_id);
        $amountUSD = round($request->amount_usd, 2);

        if ($this->checkReinvestmentMode()) {
            if ($amountUSD > $user->available_balance) {
                return back()->with('error', 'Reinvestment amount exceeds your available balance.');
            }
            return redirect()->route('user.invest')
                ->with('reinvestment_amount', $amountUSD)
                ->with('reinvestment_wallet', $wallet->id)
                ->with('info', 'Please select a plan for reinvestment.');
        }

        Session::put('deposit_details', [
            'user_id'          => $user->id,
            'wallet_id'        => $request->wallet_id,
            'amount_deposited' => $amountUSD,
            'payment_method'   => 'crypto',
        ]);

        return redirect()->route('deposit.confirm');
    }

    // ─────────────────────────────────────────
    // Confirm deposit page (crypto)
    // ─────────────────────────────────────────
    public function confirmDeposit()
    {
        if (!Session::has('deposit_details')) {
            return redirect()->route('user.deposit')
                ->withErrors(['error' => 'No deposit session found.']);
        }

        $depositDetails = Session::get('deposit_details');
        $wallet         = Wallet::findOrFail($depositDetails['wallet_id']);

        return view('dashboard.deposit.confirm-deposit', compact('wallet', 'depositDetails'));
    }

    // ─────────────────────────────────────────
    // Submit crypto deposit proof
    // ─────────────────────────────────────────
    public function submitDeposit(Request $request)
    {
        if (!Session::has('deposit_details')) {
            return redirect()->route('user.deposit-history')
                ->with('error', 'Deposit session expired.');
        }

        $request->validate([
            'proof' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $depositDetails = Session::get('deposit_details');
        $proofPath      = $request->file('proof')->store('proofs', 'public');

        $deposit = Deposit::create([
            'user_id'          => $depositDetails['user_id'],
            'wallet_id'        => $depositDetails['wallet_id'],
            'amount_deposited' => $depositDetails['amount_deposited'],
            'payment_method'   => 'crypto',
            'proof'            => $proofPath,
            'status'           => 0,
        ]);

        Session::forget('deposit_details');

        $user = User::find($deposit->user_id);
        try {
            $user->notify(new TransactionNotification(
                'Deposit Submitted',
                'Your deposit of $' . number_format($deposit->amount_deposited, 2) . ' is awaiting approval.'
            ));
        } catch (\Exception $e) {
            \Log::error('Notification failed: ' . $e->getMessage());
        }

        return redirect()->route('user.deposit-history')
            ->with('success', 'Deposit submitted successfully. Awaiting approval.');
    }

    // ─────────────────────────────────────────
    // Submit gift card deposit
    // ─────────────────────────────────────────
    public function submitGiftCard(Request $request)
    {
        $request->validate([
            'card_type'        => 'required|string|in:amazon,itunes,google,steam,walmart,other',
            'card_type_label'  => 'nullable|string|max:100',
            'other_card_name'  => 'required_if:card_type,other|nullable|string|max:100',
            'card_code'        => 'required|string|max:255',
            'amount_deposited' => 'required|numeric|min:1',
            'card_image'       => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            'notes'            => 'nullable|string|max:500',
        ]);

        $user      = auth()->user();
        $imagePath = $request->file('card_image')->store('giftcards', 'public');

        $cardLabel = $request->card_type === 'other'
            ? ($request->other_card_name ?: 'Other Gift Card')
            : ($request->card_type_label ?: ucfirst($request->card_type));

        $deposit = Deposit::create([
            'user_id'          => $user->id,
            'wallet_id'        => null,
            'amount_deposited' => round($request->amount_deposited, 2),
            'payment_method'   => 'giftcard',
            'card_type'        => $request->card_type,
            'card_type_label'  => $cardLabel,
            'other_card_name'  => $request->card_type === 'other' ? $request->other_card_name : null,
            'card_code'        => $request->card_code,
            'proof'            => $imagePath,
            'notes'            => $request->notes,
            'status'           => 0,
        ]);

        try {
            $user->notify(new TransactionNotification(
                'Gift Card Submitted',
                'Your ' . $cardLabel . ' gift card worth $' .
                number_format($deposit->amount_deposited, 2) .
                ' has been submitted and is pending verification.'
            ));
        } catch (\Exception $e) {
            \Log::error('Gift card notification failed: ' . $e->getMessage());
        }

        return redirect()->route('user.deposit-history')
            ->with('success', 'Gift card submitted successfully. Awaiting verification.');
    }

    // ─────────────────────────────────────────
    // Deposit history
    // ─────────────────────────────────────────
    public function depositHistory()
    {
        $deposits = Deposit::with(['plan', 'wallet'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('dashboard.deposit.deposit-history', compact('deposits'));
    }

    // ─────────────────────────────────────────
    // Reinvestment mode checker
    // ─────────────────────────────────────────
    protected function checkReinvestmentMode(): bool
    {
        if (session('reinvestment_mode') && session('reinvestment_expires') > now()) {
            return true;
        }
        if (session('reinvestment_expires') && session('reinvestment_expires') <= now()) {
            session()->forget(['reinvestment_mode', 'reinvestment_expires', 'reinvestment_balance']);
        }
        return false;
    }

    // ─────────────────────────────────────────
    // 1. USER REQUESTS BANK TRANSFER (No deposit created yet)
    // ─────────────────────────────────────────
    public function requestBankTransfer(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'country' => 'required|string|max:100',
        ]);

        $user = auth()->user();
        $amount = $request->amount;
        $country = $request->country;

        $existing = BankTransfer::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'details_sent'])
            ->where('expires_at', '>', now())
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a pending bank transfer request. Please wait for admin to send details.',
                'request_code' => $existing->request_code,
            ], 400);
        }

        $bankTransfer = BankTransfer::create([
            'user_id' => $user->id,
            'country' => $country,
            'amount' => $amount,
            'request_code' => BankTransfer::generateRequestCode(),
            'status' => 'pending',
            'expires_at' => now()->addHours(48),
        ]);

        $this->notifyAdminBankRequest($user, $bankTransfer, $amount);

        try {
            $user->notify(new TransactionNotification(
                'Bank Transfer Request Submitted',
                'Your bank transfer request for $' . number_format($amount, 2) . ' has been submitted. 
                An admin will send you the bank details shortly. 
                Request Code: ' . $bankTransfer->request_code
            ));
        } catch (\Exception $e) {
            Log::error('Notification failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Bank transfer request submitted! Admin will send you the bank details shortly.',
            'request_code' => $bankTransfer->request_code,
            'deposit_id' => null,
            'status' => 'pending',
        ]);
    }

    // ─────────────────────────────────────────
    // 2. CHECK BANK TRANSFER STATUS (POLLING)
    // ─────────────────────────────────────────
    public function checkBankTransferStatus(Request $request)
    {
        $bankTransfer = BankTransfer::where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'details_sent'])
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$bankTransfer) {
            return response()->json([
                'success' => false,
                'message' => 'No active bank transfer request found.',
                'status' => 'none',
            ]);
        }

        if ($bankTransfer->status === 'details_sent' && $bankTransfer->deposit && $bankTransfer->deposit->bank_details) {
            $bankDetails = $bankTransfer->deposit->bank_details;
            return response()->json([
                'success' => true,
                'status' => 'details_sent',
                'request_code' => $bankTransfer->request_code,
                'bank_details' => $bankDetails,
                'amount' => $bankTransfer->amount,
                'deposit_id' => $bankTransfer->deposit_id,
            ]);
        }

        return response()->json([
            'success' => true,
            'status' => 'pending',
            'request_code' => $bankTransfer->request_code,
            'message' => 'Waiting for admin to send bank details...',
        ]);
    }

    // ─────────────────────────────────────────
    // 3. USER SUBMITS BANK TRANSFER PROOF
    // ─────────────────────────────────────────
    public function submitBankTransferProof(Request $request)
    {
        $request->validate([
            'deposit_id' => 'required|exists:deposits,id',
            'proof' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $deposit = Deposit::findOrFail($request->deposit_id);

        if ($deposit->user_id != auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        if ($deposit->status == 1) {
            return response()->json([
                'success' => false,
                'message' => 'This deposit has already been approved.'
            ], 400);
        }

        $proofPath = $request->file('proof')->store('bank_transfer_proofs', 'public');

        $deposit->update([
            'proof' => $proofPath,
            'status' => 0,
        ]);

        $bankTransfer = BankTransfer::where('deposit_id', $deposit->id)->first();
        if ($bankTransfer) {
            $bankTransfer->update(['status' => 'completed']);
        }

        $admins = User::where('role_as', 1)->get();
        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(new AdminBankTransferProofSubmitted(
                    auth()->user(),
                    $deposit
                ));
            } catch (\Exception $e) {
                Log::error('Admin notification failed: ' . $e->getMessage());
            }
        }

        try {
            auth()->user()->notify(new TransactionNotification(
                'Bank Transfer Proof Submitted',
                'Your bank transfer proof for $' . number_format($deposit->amount_deposited, 2) . 
                ' has been submitted. Our team will review it shortly.'
            ));
        } catch (\Exception $e) {
            Log::error('User notification failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Bank transfer proof submitted successfully! Awaiting admin approval.',
        ]);
    }

    // ─────────────────────────────────────────
    // PRIVATE: Notify Admin About New Request
    // ─────────────────────────────────────────
    private function notifyAdminBankRequest($user, $bankTransfer, $amount)
    {
        try {
            $admins = User::where('role_as', 1)->get();
            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new AdminBankTransferNotification(
                    $user,
                    $bankTransfer,
                    $amount
                ));
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify admin: ' . $e->getMessage());
        }
    }
}