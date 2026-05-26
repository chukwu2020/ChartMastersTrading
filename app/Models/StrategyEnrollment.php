<?php
// app/Models/StrategyEnrollment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StrategyEnrollment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'expires_at' => 'datetime',
        'amount_paid' => 'decimal:2',
        'progress' => 'decimal:2',
    ];

    // Relationship with user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with strategy
    public function strategy()
    {
        return $this->belongsTo(Strategy::class);
    }

    // Check if enrollment is active
    public function isActive()
    {
        return $this->status === 'active' && 
               ($this->expires_at === null || $this->expires_at > now());
    }

    // Check if enrollment is expired
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at <= now();
    }

    // Mark as completed
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'progress' => 100,
            'completed_at' => now(),
        ]);
    }

    // Update progress
    public function updateProgress($percentage)
    {
        $this->update(['progress' => min(100, max(0, $percentage))]);
    }
}