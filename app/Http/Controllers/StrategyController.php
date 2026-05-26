<?php
// app/Http/Controllers/StrategyController.php

namespace App\Http\Controllers;

use App\Models\Strategy;
use App\Models\StrategyEnrollment;
use App\Notifications\TransactionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ServerFeed;
class StrategyController extends Controller
{
    /**
     * Display all available strategies/courses
     */
    public function strategyindex()
    {
        $user = auth()->user();

        // Get all active strategies
        $strategies = Strategy::active()
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get();

        // Get user's active enrollments
        $activeEnrollments = StrategyEnrollment::with('strategy')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->get();

        // Get user's completed enrollments
        $completedEnrollments = StrategyEnrollment::with('strategy')
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->latest()
            ->get();

        return view('dashboard.strategies.index', compact(
            'strategies',
            'activeEnrollments',
            'completedEnrollments'
        ));
    }

    /**
     * Show a specific strategy/course details
     */
    public function strategyshow($id)
    {
        $user = auth()->user();
        $strategy = Strategy::findOrFail($id);

        // Check if user is already enrolled
        $enrollment = $strategy->getUserEnrollment($user->id);

        // Get all available upgrades
        $upgrades = Strategy::active()
            ->where('price', '>', $strategy->price)
            ->orderBy('price')
            ->get();
  $adminProfile = ServerFeed::latest()->first();
        return view('dashboard.strategies.show', compact(
            'strategy',
            'enrollment',
            'upgrades',
             'adminProfile'
        ));
    }

    /**
     * Enroll in a strategy/course
     */
    public function strategyenroll(Request $request, $id)
    {
        $user = auth()->user();
        $strategy = Strategy::findOrFail($id);

        // Check if already enrolled
        if ($strategy->isUserEnrolled($user->id)) {
            return redirect()->route('strategies.strategyshow', $strategy->id)
                ->with('error', 'You are already enrolled in this course.');
        }

        // Check if user has sufficient balance
        if ($user->available_balance < $strategy->price) {
            return redirect()->route('strategies.strategyshow', $strategy->id)
                ->with('error', 'Insufficient balance. Please deposit funds.');
        }

        DB::transaction(function () use ($user, $strategy) {
            // Deduct amount from user balance
            $user->decrement('available_balance', $strategy->price);

            // Calculate expiry date if applicable
            $expiresAt = null;
            if ($strategy->duration_days > 0) {
                $expiresAt = now()->addDays($strategy->duration_days);
            }

            // Create enrollment
            StrategyEnrollment::create([
                'user_id' => $user->id,
                'strategy_id' => $strategy->id,
                'amount_paid' => $strategy->price,
                'status' => 'active',
                'enrolled_at' => now(),
                'expires_at' => $expiresAt,
                'progress' => 0,
            ]);

            // Send notification
            try {
                $user->notify(new TransactionNotification(
                    'Course Enrollment Successful',
                    "You have successfully enrolled in \"{$strategy->name}\" for \${$strategy->price}.\n" .
                        "Start learning now!"
                ));
            } catch (\Exception $e) {
                \Log::error('Notification failed: ' . $e->getMessage());
            }
        });

        // ✅ FIXED: Use correct route name 'strategies.strategyshow'
        return redirect()->route('strategies.strategyshow', $strategy->id)
            ->with('success', 'Successfully enrolled in the course!');
    }

    /**
     * Upgrade to a higher strategy/course
     */
    public function strategyupgrade(Request $request, $currentId, $newId)
    {
        $user = auth()->user();
        $currentStrategy = Strategy::findOrFail($currentId);
        $newStrategy = Strategy::findOrFail($newId);

        // Check if user is enrolled in current course
        $currentEnrollment = $currentStrategy->getUserEnrollment($user->id);
        if (!$currentEnrollment) {
            return redirect()->route('strategies.strategyshow', $currentStrategy->id)
                ->with('error', 'You are not enrolled in this course.');
        }

        // Check if new price is higher
        if ($newStrategy->price <= $currentStrategy->price) {
            return redirect()->route('strategies.strategyshow', $currentStrategy->id)
                ->with('error', 'Invalid upgrade path.');
        }

        // Calculate upgrade fee (difference)
        $upgradeFee = $newStrategy->price - $currentStrategy->price;

        // Check if user has sufficient balance
        if ($user->available_balance < $upgradeFee) {
            return redirect()->route('strategies.strategyshow', $currentStrategy->id)
                ->with('error', "Insufficient balance. Need \${$upgradeFee} to upgrade.");
        }

        DB::transaction(function () use ($user, $currentStrategy, $newStrategy, $currentEnrollment, $upgradeFee) {
            // Deduct upgrade fee
            $user->decrement('available_balance', $upgradeFee);

            // Mark old enrollment as upgraded
            $currentEnrollment->update([
                'status' => 'upgraded',
                'upgraded_to' => $newStrategy->id,
            ]);

            // Calculate new expiry date
            $expiresAt = null;
            if ($newStrategy->duration_days > 0) {
                $expiresAt = now()->addDays($newStrategy->duration_days);
            }

            // Create new enrollment
            StrategyEnrollment::create([
                'user_id' => $user->id,
                'strategy_id' => $newStrategy->id,
                'amount_paid' => $newStrategy->price,
                'status' => 'active',
                'enrolled_at' => now(),
                'expires_at' => $expiresAt,
                'progress' => 0,
                'upgraded_from' => $currentStrategy->id,
            ]);

            // Send notification
            try {
                $user->notify(new TransactionNotification(
                    'Course Upgrade Successful',
                    "You have successfully upgraded from \"{$currentStrategy->name}\" to \"{$newStrategy->name}\".\n" .
                        "Upgrade fee: \${$upgradeFee}"
                ));
            } catch (\Exception $e) {
                \Log::error('Notification failed: ' . $e->getMessage());
            }
        });

        // ✅ FIXED: Use correct route name 'strategies.strategyshow'
        return redirect()->route('strategies.strategyshow', $newStrategy->id)
            ->with('success', 'Successfully upgraded to the new course!');
    }

    /**
     * Show learning content for enrolled course
     */
    public function strategylearn($id)
    {
        $user = auth()->user();
        $strategy = Strategy::findOrFail($id);

        // Verify user is enrolled
        $enrollment = $strategy->getUserEnrollment($user->id);
        if (!$enrollment) {
            return redirect()->route('strategies.strategyshow', $strategy->id);
        }

        // Check if expired
        if ($enrollment->isExpired()) {
            $enrollment->update(['status' => 'expired']);
            return redirect()->route('strategies.strategyshow', $strategy->id);
        }
 $adminProfile = ServerFeed::latest()->first();
        return view('dashboard.strategies.learn', compact('strategy', 'enrollment', 'adminProfile'));
    }
}