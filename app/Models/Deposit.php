<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'status' => 'integer',
        'bank_details_sent_at' => 'datetime',
        'bank_details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    // ─────────────────────────────────────────
    // Bank Transfer Relationship
    // ─────────────────────────────────────────
    public function bankTransfer()
    {
        return $this->hasOne(BankTransfer::class);
    }

    // ─────────────────────────────────────────
    // Get Bank Details as Array
    // ─────────────────────────────────────────
    public function getBankDetailsAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function setBankDetailsAttribute($value)
    {
        $this->attributes['bank_details'] = json_encode($value);
    }

    // ─────────────────────────────────────────
    // Check if deposit is a bank transfer
    // ─────────────────────────────────────────
    public function isBankTransfer()
    {
        return ($this->payment_method ?? '') === 'bank_transfer';
    }

    // ─────────────────────────────────────────
    // Check if bank details have been sent
    // ─────────────────────────────────────────
    public function getBankDetailsSentAttribute()
    {
        return !is_null($this->bank_details_sent_at);
    }
}