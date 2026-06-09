<?php
// app/Models/UserTradingInfo.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTradingInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stock_experience',
        'trading_frequency',
        'transaction_volume',
        'investment_goals',
        'asset_classes',
        'account_type',
        'copy_admin_id',
        'copy_admin_name',
        'copy_server_name',
        'investment_amount',
        'financial_alternative',
        'annual_income',
        'deposit_source',
        'learning_style',
        'ongoing_deposit_source',
    ];

    protected $casts = [
        'investment_goals' => 'array',
        'asset_classes' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(ServerFeed::class, 'copy_admin_id');
    }
}