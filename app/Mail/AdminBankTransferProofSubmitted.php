<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Deposit;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminBankTransferProofSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $deposit;

    public function __construct(User $user, Deposit $deposit)
    {
        $this->user = $user;
        $this->deposit = $deposit;
    }

    public function build()
    {
        return $this->subject('📎 Bank Transfer Proof Submitted - ChartMasters')
                    ->markdown('emails.admin-bank-transfer-proof');
    }
}