<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\Withdrawal;
use App\Models\WithdrawalCard;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function index()
    {
        $withdrawals = Withdrawal::with(['investment.plan', 'user.profile'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('dashboard.withdrawal.index', compact('withdrawals'));
    }

    public function generateCard(Request $request)
    {
        $user = Auth::user();

        if ($user->withdrawalCard) {
            return back()->with('error', 'Card already generated.');
        }

        WithdrawalCard::create([
            'user_id'      => $user->id,
            'card_number'  => str_pad(mt_rand(0, 9999999999999999), 16, '0', STR_PAD_LEFT),
            'pin'          => rand(1000, 9999),
            'name_on_card' => $user->name,
        ]);

        return back()->with('success', 'Withdrawal card generated!');
    }

    public function viewCard()
    {
        $card = WithdrawalCard::where('user_id', Auth::id())->first();

        if (!$card) {
            return redirect()->route('withdrawals.generateCard')
                ->with('error', 'No card found. Please generate one first.');
        }

        return view('dashboard.user.card', compact('card'));
    }

    /**
     * Show the withdraw form — now computes the session/lock status
     * up front (server-side), the same way the copy-trading plan grid
     * computes $isLimitReached at render time. No more AJAX guesswork
     * at the moment the user clicks submit.
     */
    public function showWithdrawForm()
    {
        $user = Auth::user();

        if (!$user->withdrawalCard) {
            return back()->with('error', 'Please generate your withdrawal card before proceeding.');
        }

        $lockData = $this->buildLockData($user);

        return view('dashboard.withdrawal.withdrawer', compact('lockData'));
    }

    public function withdrawalList()
    {
        $withdrawals = auth()->user()
            ->withdrawals()
            ->with(['investment.plan', 'user.profile'])
            ->latest()
            ->get();

        return view('dashboard.withdrawal.index', compact('withdrawals'));
    }

    /**
     * AJAX: calculate fees preview for the withdrawal form.
     * Balance withdrawals only ever incur a bank transfer fee (if applicable).
     * Management & performance fees are NEVER charged on balance withdrawals.
     */
    public function calculateFees(Request $request)
    {
        $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|in:cryptocurrency,digital_wallet',
        ]);

        $grossAmount   = (float) $request->amount;
        $paymentMethod = $request->payment_method ?? 'cryptocurrency';

        $bankFee = $paymentMethod === 'digital_wallet'
            ? round($grossAmount * 0.05, 2)
            : 0.0;

        $totalFees = $bankFee;
        $netAmount = round($grossAmount - $totalFees, 2);

        return response()->json([
            'total_management_fee'  => 0,
            'total_performance_fee' => 0,
            'bank_fee'              => $bankFee,
            'total_fees'            => $totalFees,
            'net_amount'            => $netAmount,
            'fee_breakdown'         => [],
        ]);
    }

    /**
     * AJAX safety-net endpoint. The primary source of truth is now the
     * server-rendered $lockData passed into the blade at page load — this
     * route is kept only in case the user sits on the page a while and
     * their lock state changes before they submit. It now fails CLOSED
     * (returns locked => true with an 'error' flag) instead of silently
     * pretending everything is fine when something throws.
     */
    public function checkWithdrawalLock()
    {
        $user = auth()->user();

        try {
            $data = $this->buildLockData($user);
            return response()->json($data);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'locked'    => true,
                'error'     => true,
                'completed' => 0,
                'required'  => 0,
                'plan_name' => null,
                'message'   => 'We could not verify your withdrawal eligibility. Please refresh and try again.',
            ], 200);
        }
    }

    /**
     * Shared logic for building the lock/session payload, used both by
     * the page load (showWithdrawForm) and the AJAX safety-net route.
     */
    private function buildLockData(User $user): array
    {
        $data = [
            'locked'    => (bool) ($user->withdrawal_locked ?? false),
            'completed' => 0,
            'required'  => 0,
            'plan_name' => null,
            'reason'    => null,
        ];

        if (!$data['locked']) {
            return $data;
        }

        $incomplete = Investment::with('plan')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->get()
            ->first(function ($inv) {
                return method_exists($inv, 'hasCompletedRequiredSessions')
                    ? !$inv->hasCompletedRequiredSessions()
                    : true;
            });

        if ($incomplete) {
            $data['completed']  = $incomplete->completed_sessions ?? 0;
            $data['required']   = optional($incomplete->plan)->trading_sessions ?? 0;
            $data['plan_name']  = optional($incomplete->plan)->name ?? 'Active Plan';
            $data['reason']     = 'sessions_incomplete';
        } else {
            // Locked, but no active investment with incomplete sessions found —
            // e.g. an admin locked the account for another reason. Don't show
            // a misleading 0/0 progress bar.
            $data['reason'] = 'admin_locked';
        }

        return $data;
    }

    /**
     * Process a balance withdrawal (external — crypto or bank transfer).
     * Only a 5% bank transfer fee applies. No management or performance fees.
     */
    public function withdrawFromBalance(Request $request)
    {
        $cardPin = $request->digit1 . $request->digit2
                 . $request->digit3 . $request->digit4;
        $request->merge(['card_pin' => $cardPin]);

        $request->validate([
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|in:cryptocurrency,digital_wallet',
            'card_pin'       => 'required|string|size:4',
            'wallet_choice'  => 'required|string',
        ]);

        return DB::transaction(function () use ($request) {

            /** @var \App\Models\User $user */
            $user = User::where('id', auth()->id())->lockForUpdate()->first();
            $card = WithdrawalCard::where('user_id', $user->id)->first();

            if (!$card || (string) $card->pin !== (string) $request->card_pin) {
                return back()->with('error', 'Incorrect card PIN.');
            }

            // ── WITHDRAWAL LOCK CHECK ──────────────────────────────────
            // Kept here as the final server-side guard, but the UI should
            // now catch this *before* the request ever gets here, via the
            // server-rendered $lockData shown on page load.
            if ($user->withdrawal_locked) {
                return back()->with('error',
                    'Complete your training section before withdrawing. ' .
                    'Contact support if you believe this is an error.'
                );
            }

            $grossAmount = (float) $request->amount;

            if ($grossAmount > $user->available_balance) {
                return back()->with('error', 'Insufficient balance.');
            }

            $recentPending = Withdrawal::where('user_id', $user->id)
                ->where('status', 'pending')
                ->where('created_at', '>=', now()->subMinute())
                ->exists();

            if ($recentPending) {
                return back()->with('error', 'Please wait a moment before submitting another request.');
            }

            $bankFee = $request->payment_method === 'digital_wallet'
                ? round($grossAmount * 0.05, 2)
                : 0.0;

            $totalFees = $bankFee;
            $netAmount = round($grossAmount - $totalFees, 2);

            if ($netAmount <= 0) {
                return back()->with('error',
                    'Your withdrawal amount ($' . number_format($grossAmount, 2) . ') '
                    . 'is fully consumed by the bank transfer fee ($' . number_format($totalFees, 2) . '). '
                    . 'Please withdraw a larger amount.'
                );
            }

            Withdrawal::create([
                'user_id'         => $user->id,
                'amount'          => $grossAmount,
                'net_amount'      => $netAmount,
                'management_fee'  => 0,
                'performance_fee' => 0,
                'bank_fee'        => $bankFee,
                'fee_breakdown'   => [],
                'payment_method'  => $request->payment_method,
                'wallet_choice'   => $request->wallet_choice,
                'status'          => 'pending',
                'investment_id'   => null,
                'type'            => Withdrawal::TYPE_BALANCE,
            ]);

            $user->available_balance -= $grossAmount;
            $user->save();

            $message = $bankFee > 0
                ? sprintf(
                    'Withdrawal submitted! Amount: $%s | Bank Fee (5%%): $%s | You receive: $%s',
                    number_format($grossAmount, 2),
                    number_format($bankFee, 2),
                    number_format($netAmount, 2)
                )
                : sprintf(
                    'Withdrawal submitted! Amount: $%s — no fees applied.',
                    number_format($grossAmount, 2)
                );

            return redirect()->route('user.withdrawals.list')->with('success', $message);
        });
    }
}