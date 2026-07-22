<?php
// app/Models/Investment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Investment extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 'active';
    const STATUS_WITHDRAWN = 'withdrawn';
    const STATUS_COMPLETED = 'completed';
    
    protected $guarded = [];

    protected $casts = [
        'current_value' => 'decimal:2',
        'profit_loss' => 'decimal:2',
        'amount_invested' => 'decimal:2',
        'expected_profit' => 'decimal:2',
        'total_profit' => 'decimal:2',
        'assets_traded' => 'array',
        'snapshot_features' => 'array',
        'snapshot_assets_traded' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

public function getIsActiveAttribute()
{
    return now()->lt($this->end_date) && $this->status !== self::STATUS_WITHDRAWN;
}
    public function getIsCompletedAttribute()
{
    return now()->gte($this->end_date);
}
    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    // Get the duration value (uses snapshot if available, falls back to plan)
    public function getDurationValueAttribute()
    {
        if ($this->snapshot_duration_value) {
            return $this->snapshot_duration_value;
        }
        return $this->plan ? $this->plan->duration : 0;
    }

    // Get the duration unit (uses snapshot if available, falls back to plan)
    public function getDurationUnitAttribute()
    {
        if ($this->snapshot_duration_unit) {
            return $this->snapshot_duration_unit;
        }
        return $this->plan ? ($this->plan->duration_unit ?? 'days') : 'days';
    }

    // Get the interest rate (uses snapshot if available, falls back to plan)
    public function getInvestmentInterestRateAttribute()
    {
        if ($this->snapshot_interest_rate) {
            return $this->snapshot_interest_rate;
        }
        return $this->plan ? $this->plan->interest_rate : 0;
    }

    // Get the plan name (uses snapshot if available, falls back to plan)
    public function getInvestmentPlanNameAttribute()
    {
        if ($this->snapshot_plan_name) {
            return $this->snapshot_plan_name;
        }
        return $this->plan ? $this->plan->name : 'N/A';
    }

    // Calculate expected profit based on amount and interest rate
    public function calculateExpectedProfit()
    {
        $rate = $this->getInvestmentInterestRateAttribute();
        return ($this->amount_invested * $rate) / 100;
    }

    // Calculate current profit based on expected profit and progress
    public function calculateCurrentProfit()
    {
        $expectedProfit = $this->calculateExpectedProfit();
        $progress = $this->progress_percentage / 100;
        
        // For completed trades, return full profit
        if ($this->status === 'completed' || $this->progress_percentage >= 100) {
            return $expectedProfit;
        }
        
        // For active trades, return proportional profit based on time elapsed
        return $expectedProfit * $progress;
    }

    // Update current value and profit loss
    public function updateValue()
    {
        $currentProfit = $this->calculateCurrentProfit();
        $this->profit_loss = $currentProfit;
        $this->current_value = $this->amount_invested + $currentProfit;
        $this->save();
        
        return $this;
    }

    // Get formatted duration
    public function getFormattedDurationAttribute()
    {
        $value = $this->duration_value;
        $unit = $this->duration_unit;
        
        if ($value > 1) {
            $unit = rtrim($unit, 's') . 's';
        }
        
        return $value . ' ' . $unit;
    }

    // Get profit/loss percentage
    public function getProfitLossPercentageAttribute()
    {
        if ($this->amount_invested > 0) {
            return round(($this->profit_loss / $this->amount_invested) * 100, 2);
        }
        return 0;
    }

    // Get profit/loss color class
    public function getProfitLossColorAttribute()
    {
        if ($this->profit_loss > 0) return 'text-green-600';
        if ($this->profit_loss < 0) return 'text-red-600';
        return 'text-gray-500';
    }

    // Get profit/loss icon
    public function getProfitLossIconAttribute()
    {
        if ($this->profit_loss > 0) return 'ph:trend-up-fill';
        if ($this->profit_loss < 0) return 'ph:trend-down-fill';
        return 'ph:minus-fill';
    }

    // Check if investment is completed
   
   
    // Get time remaining with proper unit display
    public function getTimeRemainingAttribute()
    {
        $now = now();
        $end = Carbon::parse($this->end_date);
        
        if ($now->gte($end)) {
            return 'Completed';
        }
        
        $diff = $now->diff($end);
        
        if ($diff->days > 0) {
            return "{$diff->days} days remaining";
        } elseif ($diff->h > 0) {
            return "{$diff->h} hours remaining";
        } elseif ($diff->i > 0) {
            return "{$diff->i} minutes remaining";
        } else {
            return "{$diff->s} seconds remaining";
        }
    }

    // Get progress percentage based on actual time
    public function getProgressPercentageAttribute()
    {
        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        $now = now();
        
        if ($now->gte($end)) return 100;
        if ($now->lte($start)) return 0;
        
        $totalSeconds = $start->diffInSeconds($end);
        $elapsedSeconds = $start->diffInSeconds($now);
        
        return round(($elapsedSeconds / $totalSeconds) * 100, 2);
    }

    // Check if withdrawn
    public function isWithdrawn()
    {
        return $this->status === self::STATUS_WITHDRAWN;
    }

    // In your Investment model, add a mutator for end_date
public function setEndDateAttribute($value)
{
    \Log::info('Setting end_date: ' . $value);
    $this->attributes['end_date'] = $value;
}


public function hasCompletedRequiredSessions(): bool
{
    $required = $this->plan->trading_sessions ?? 0;
    
    if ($required <= 0) {
        return true; // No sessions required
    }
    
    return ($this->completed_sessions ?? 0) >= $required;
}
public function calculateActualProfitEarned()
{
    $totalExpectedProfit = ($this->amount_invested * $this->plan->interest_rate) / 100;
    $progress = $this->progress_percentage / 100;
    $actualProfitEarned = $totalExpectedProfit * $progress;
    
    // Update profit_earned field
    $this->profit_earned = $actualProfitEarned;
    $this->save();
    
    return $actualProfitEarned;
}


// Add this method to your Investment model
public static function updateAllActiveProfits()
{
    $investments = self::where('status', 'active')->get();
    foreach ($investments as $investment) {
        $investment->calculateActualProfitEarned();
    }
}
}