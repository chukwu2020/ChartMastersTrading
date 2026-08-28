<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'deposit_id',
        'country',
        'amount',
        'request_code',
        'status',
        'bank_details_sent_at',
        'expires_at',
    ];

    protected $casts = [
        'status' => 'string',
        'bank_details_sent_at' => 'datetime',
        'expires_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deposit()
    {
        return $this->belongsTo(Deposit::class);
    }

    public static function generateRequestCode()
    {
        do {
            $code = 'BT' . strtoupper(uniqid()) . rand(1000, 9999);
        } while (self::where('request_code', $code)->exists());

        return $code;
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Pending',
            'details_sent' => 'Details Sent',
            'completed' => 'Completed',
            'expired' => 'Expired',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'details_sent' => 'green',
            'completed' => 'blue',
            'expired' => 'red',
            default => 'gray',
        };
    }
}