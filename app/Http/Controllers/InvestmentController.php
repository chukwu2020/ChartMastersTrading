<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;

class InvestmentController extends Controller
{
    public function index()
    {
        $investments = Investment::with('withdrawals', 'plan')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        // ACTIVE = not yet finished AND not withdrawn
        $activeInvestments = $investments->filter(function ($inv) {
            return $inv->status !== Investment::STATUS_WITHDRAWN
                && now()->lt($inv->end_date);
        });

        // COMPLETED = finished AND not withdrawn
        $completedInvestments = $investments->filter(function ($inv) {
            return $inv->status !== Investment::STATUS_WITHDRAWN
                && now()->gte($inv->end_date);
        });

        return view('dashboard.investments.index', compact(
            'activeInvestments',
            'completedInvestments'
        ));
    }

    public function withdrawnInvestments()
    {
        $withdrawnInvestments = Investment::with('plan')
            ->where('user_id', auth()->id())
            ->where('status', Investment::STATUS_WITHDRAWN)
            ->latest()
            ->get();

        return view('dashboard.investments.investmentList', compact('withdrawnInvestments'));
    }

    /**
     * Full withdrawal of a COMPLETED investment (capital + remaining profit, minus fees).
     */
    public function withdraw($id)
    {
        return DB::transaction(function () use ($id) {

            $investment = Investment::where('id', $id)
                ->where('user_id', auth()->id())
                ->lockForUpdate()
                ->firstOrFail();

            if ($investment->status === 'withdrawn') {
                return redirect()->route('user.investments')
                    ->with('error', 'Investment already withdrawn.');
            }

            if (now()->lt($investment->end_date)) {
                return redirect()->route('user.investments')
                    ->with('error', 'Investment is not yet completed. Please wait until the end date.');
            }

            $user = \App\Models\User::where('id', auth()->id())
                ->lockForUpdate()
                ->first();

            // ── Total profit for this investment ──────────────────────

            $totalProfit = ($investment->amount_invested * $investment->plan->interest_rate) / 100;

            // Profit already paid out via take-profit withdrawals
            $alreadyTakenProfit = $investment->withdrawals()
                ->where('type', Withdrawal::TYPE_PROFIT)
                ->sum('amount');

            // Remaining profit still owed (after any earlier take-profits)
            $remainingProfit = max($totalProfit - $alreadyTakenProfit, 0);

            // ── Fee calculation on remaining profit ───────────────────

            $managementFeePercent  = floatval($investment->plan->management_fee  ?? 0);
            $performanceFeePercent = floatval($investment->plan->performance_fee ?? 0);

            // Only charge on profit not yet had fees deducted
            $alreadyDeductedMgmt = floatval($investment->management_fee_deducted ?? 0);
            $alreadyDeductedPerf = floatval($investment->performance_fee_deducted ?? 0);

            $chargeableMgmt = max(0, $remainingProfit - $alreadyDeductedMgmt);
            $chargeablePerf = max(0, $remainingProfit - $alreadyDeductedPerf);

            $managementFee  = round(($chargeableMgmt * $managementFeePercent)  / 100, 2);
            $performanceFee = round(($chargeablePerf * $performanceFeePercent) / 100, 2);
            $totalFees      = $managementFee + $performanceFee;

            // Gross = capital + remaining profit
            $grossAmount = round($investment->amount_invested + $remainingProfit, 2);

            // Net = gross minus fees. Capital is NEVER touched by fees.
            $netAmount = round($grossAmount - $totalFees, 2);
            $netAmount = max($netAmount, (float) $investment->amount_invested); // safety floor

            // ── Credit user ───────────────────────────────────────────

            $user->available_balance += $netAmount;
            $user->save();

            // ── Update investment fee tracking ────────────────────────

            $investment->update([
                'status'                   => 'withdrawn',
                'withdrawn_at'             => now(),
                'final_profit_withdrawn'   => $remainingProfit,
                'management_fee_deducted'  => $alreadyDeductedMgmt + $managementFee,
                'performance_fee_deducted' => $alreadyDeductedPerf + $performanceFee,
            ]);

            // ── Record the withdrawal ─────────────────────────────────

            Withdrawal::create([
                'user_id'         => $user->id,
                'investment_id'   => $investment->id,
                'amount'          => $grossAmount,
                'net_amount'      => $netAmount,
                'management_fee'  => $managementFee,
                'performance_fee' => $performanceFee,
                'bank_fee'        => 0,
                'fee_breakdown'   => [[
                    'investment_id'              => $investment->id,
                    'plan_name'                  => $investment->plan->name,
                    'amount_invested'            => (float) $investment->amount_invested,
                    'profit_earned'              => round($remainingProfit, 2),
                    'management_fee_percentage'  => $managementFeePercent,
                    'management_fee'             => $managementFee,
                    'performance_fee_percentage' => $performanceFeePercent,
                    'performance_fee'            => $performanceFee,
                ]],
                'type'            => Withdrawal::TYPE_INVESTMENT,
                'status'          => 'approved',
                'payment_method'  => 'internal',
                'wallet_choice'   => 'balance',
            ]);

            return redirect()->route('user.withdrawn.investments')
                ->with('success', sprintf(
                    'Withdrawal successful! Capital + Profit: $%s | Platform Fees: $%s | Credited to balance: $%s',
                    number_format($grossAmount, 2),
                    number_format($totalFees, 2),
                    number_format($netAmount, 2)
                ));

        });
    }

    /**
     * Take partial profit from an ACTIVE (not yet completed) investment, with fees deducted.
     */
    public function takeProfit($id)
    {
        return DB::transaction(function () use ($id) {

            $investment = Investment::where('id', $id)
                ->where('user_id', auth()->id())
                ->lockForUpdate()
                ->firstOrFail();

            // Cannot take profit from a completed investment — use full withdrawal instead
            if (now()->gte($investment->end_date)) {
                return back()->with('error', 'Investment is completed. Please use full withdrawal to claim your funds.');
            }

            if ($investment->status === 'withdrawn') {
                return back()->with('error', 'Investment already withdrawn.');
            }

            // Must wait at least 5 hours after investing
            if (now()->lt($investment->created_at->addHours(5))) {
                return back()->with('error', 'You can only take profit after 5 hours of investing.');
            }

            $user = \App\Models\User::where('id', auth()->id())
                ->lockForUpdate()
                ->first();

            // ── Calculate earned profit so far ────────────────────────

            // progress_percentage is 0–100. We divide by 100 to get a fraction.
            $progress            = max(min($investment->progress_percentage, 100), 0) / 100;
            $totalExpectedProfit = ($investment->amount_invested * $investment->plan->interest_rate) / 100;
            $earnedProfit        = round($totalExpectedProfit * $progress, 2); // ✅ FIXED: was dividing by 100 twice

            // Profit already paid out previously
            $alreadyTakenProfit = $investment->withdrawals()
                ->where('type', Withdrawal::TYPE_PROFIT)
                ->sum('amount');

            // Profit available to take RIGHT NOW (before fees)
            $availableProfit = max($earnedProfit - $alreadyTakenProfit, 0);

            // ── Take-profit cap ───────────────────────────────────────

            // Users can take a maximum of $30 (or $10 for investments < $12,000) in profit
            // before the investment completes. This is the RAW profit cap (pre-fee).
            $maxAllowed     = $investment->amount_invested >= 12000 ? 30 : 10;
            $remainingLimit = max($maxAllowed - $alreadyTakenProfit, 0);

            if ($alreadyTakenProfit >= $maxAllowed) {
                return back()->with('error',
                    "You have reached the maximum take-profit limit of \${$maxAllowed}. "
                    . "Please wait for the investment to complete for full withdrawal."
                );
            }

            if ($availableProfit <= 0) {
                return back()->with('error', 'No profit available yet. Please wait for more progress.');
            }

            // Gross take amount = min of what's available vs remaining cap
            $grossTakeAmount = round(min($availableProfit, $remainingLimit), 2);

            // ── Fee deduction on this take-profit ─────────────────────

            $managementFeePercent  = floatval($investment->plan->management_fee  ?? 0);
            $performanceFeePercent = floatval($investment->plan->performance_fee ?? 0);

            // Only charge on profit that hasn't already had fees deducted
            $alreadyDeductedMgmt = floatval($investment->management_fee_deducted ?? 0);
            $alreadyDeductedPerf = floatval($investment->performance_fee_deducted ?? 0);

            $chargeableMgmt = max(0, $grossTakeAmount - $alreadyDeductedMgmt);
            $chargeablePerf = max(0, $grossTakeAmount - $alreadyDeductedPerf);

            $managementFee  = round(($chargeableMgmt * $managementFeePercent)  / 100, 2);
            $performanceFee = round(($chargeablePerf * $performanceFeePercent) / 100, 2);
            $totalFees      = $managementFee + $performanceFee;

            // Net amount the user actually receives
            $netAmount = max(round($grossTakeAmount - $totalFees, 2), 0);

            if ($netAmount <= 0) {
                return back()->with('error',
                    'The available profit ($' . number_format($grossTakeAmount, 2) . ') '
                    . 'is fully consumed by platform fees. More profit must accumulate first.'
                );
            }

            // ── Credit user with net profit ───────────────────────────

            $user->available_balance += $netAmount;
            $user->save();

            // ── Track fee deductions on the investment ────────────────

            $investment->management_fee_deducted  = $alreadyDeductedMgmt + $managementFee;
            $investment->performance_fee_deducted = $alreadyDeductedPerf + $performanceFee;
            $investment->save();

            // ── Record the take-profit withdrawal ─────────────────────

            // NOTE: 'amount' records the GROSS take (pre-fee) so alreadyTakenProfit
            // cap tracking stays correct. net_amount shows what user received.
            Withdrawal::create([
                'user_id'         => $user->id,
                'investment_id'   => $investment->id,
                'amount'          => $grossTakeAmount,   // gross (used for cap tracking)
                'net_amount'      => $netAmount,         // what user actually received
                'management_fee'  => $managementFee,
                'performance_fee' => $performanceFee,
                'bank_fee'        => 0,
                'fee_breakdown'   => [[
                    'investment_id'              => $investment->id,
                    'plan_name'                  => $investment->plan->name,
                    'amount_invested'            => (float) $investment->amount_invested,
                    'profit_earned'              => round($earnedProfit, 2),
                    'management_fee_percentage'  => $managementFeePercent,
                    'management_fee'             => $managementFee,
                    'performance_fee_percentage' => $performanceFeePercent,
                    'performance_fee'            => $performanceFee,
                ]],
                'type'            => Withdrawal::TYPE_PROFIT,
                'status'          => 'approved',
                'payment_method'  => 'internal',
                'wallet_choice'   => 'balance',
            ]);

            return back()->with('success', sprintf(
                'Profit taken! Gross: $%s | Platform Fees: $%s | Credited to balance: $%s',
                number_format($grossTakeAmount, 2),
                number_format($totalFees, 2),
                number_format($netAmount, 2)
            ));

        });
    }

    public function updateInvestmentProfit($investmentId, $newProfit)
    {
        $investment = Investment::findOrFail($investmentId);
        $investment->profit_earned = $newProfit;
        $investment->save();

        return response()->json(['success' => true]);
    }
}