<?php
// app/Models/Strategy.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Strategy extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'features' => 'array',
        'modules' => 'array',
         'learning_objectives' => 'array', 
        'prerequisites' => 'array',     
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
        'price' => 'decimal:2',
    ];

    // Relationship with user enrollments
    public function enrollments()
    {
        return $this->hasMany(StrategyEnrollment::class);
    }

    // Get active enrollments
    public function activeEnrollments()
    {
        return $this->hasMany(StrategyEnrollment::class)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    // Check if user is enrolled
    public function isUserEnrolled($userId)
    {
        return $this->enrollments()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->exists();
    }

    // Get user's enrollment
    public function getUserEnrollment($userId)
    {
        return $this->enrollments()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();
    }

    // Scope for active strategies
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}