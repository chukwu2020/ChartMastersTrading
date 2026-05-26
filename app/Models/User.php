<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'available_balance' => 'float',
    ];

    protected $attributes = [
        'available_balance' => 0.00,
    ];

    // Relationships
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }
    
    public function investments()
    {
        return $this->hasMany(Investment::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function withdrawalCard()
    {
        return $this->hasOne(WithdrawalCard::class);
    }

 
    // In app/Models/User.php - add these methods

public function userKyc()
{
    return $this->hasOne(UserKyc::class)->latest();
}

public function getKycStatusAttribute()
{
    return $this->userKyc?->status ?? 'not_submitted';
}

public function isKycVerified()
{
    return $this->userKyc && $this->userKyc->status === 'approved';
}
 // Role
    public function role()
    {
        return $this->role_as === '1' ? 'admin' : 'user';
    }

  


// In app/Models/User.php
// Remove or comment out the existing getAmountInvestedAttribute and replace with:


// In app/Models/User.php


public function getAmountInvestedAttribute()
{
    return (float) $this->deposits()
        ->where('status', 1)
        ->sum('amount_deposited');
}

// In app/Models/User.php

public function tradingInfo()
{
    return $this->hasOne(UserTradingInfo::class, 'user_id');
}
    public function getTotalInterestEarnedAttribute()
    {
        return $this->investments()
            ->where('status', 'completed')
            ->get()
            ->sum('total_profit');
    }

    public function getTotalWithdrawnAttribute()
    {
        return $this->withdrawals()
                    ->where('status', 'approved')
                    ->sum('amount');
    }

    public function getTotalIncomeAttribute()
    {
        return $this->available_balance + $this->total_interest_earned;
    }

    public function getTotalWithdrawalsAttribute()
    {
        return $this->withdrawals()->sum('amount');
    }

    // MEMBERSHIP & ACCESS METHODS
    /**
     * Check if user has premium access ($25,000+ plan)
     */
    public function hasPremiumAccess()
    {
        return $this->investments()
            ->where('status', 'completed')
            ->where('amount_invested', '>=', 25000)
            ->exists();
    }

    /**
     * Get user's total investment amount
     */
    public function getTotalInvestmentAmount()
    {
        return $this->investments()
            ->where('status', 'completed')
            ->sum('amount_invested');
    }

    /**
     * Check if user has approved deposit
     */
  

/**
 * Check if user has approved deposit (status = 1)
 */
public function hasApprovedDeposit()
{
    return $this->deposits()->where('status', 1)->exists();
}

/**
 * Check if user has active membership
 */
public function hasActiveMembership()
{
    return !empty($this->membership_code) && $this->has_membership == 1;
}
    /**
     * Get user's highest investment amount
     */
    public function getHighestInvestmentAmount()
    {
        return $this->investments()
            ->where('status', 'completed')
            ->max('amount_invested') ?? 0;
    }

    /**
     * Check if user has premium tier investment
     */
    public function hasPremiumTierInvestment()
    {
        return $this->investments()
            ->where('status', 'completed')
            ->where('amount_invested', '>=', 25000)
            ->exists();
    }


    // App\Models\User.php

public function activePlan()
{
    return $this->hasOne(Investment::class)->where('status', 'active');
}


// In app/Models/User.php - add these relationships

public function strategyEnrollments()
{
    return $this->hasMany(StrategyEnrollment::class);
}

public function activeStrategies()
{
    return $this->belongsToMany(Strategy::class, 'strategy_enrollments')
        ->wherePivot('status', 'active')
        ->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
}

public function hasActiveStrategy($strategyId)
{
    return $this->activeStrategies()->where('strategies.id', $strategyId)->exists();
}

}