<?php

namespace App\Mail;

use App\Models\User;
use App\Models\BankTransfer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminBankTransferNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $bankTransfer;
    public $amount;

    public function __construct(User $user, BankTransfer $bankTransfer, $amount)
    {
        $this->user = $user;
        $this->bankTransfer = $bankTransfer;
        $this->amount = $amount;
    }

    public function build()
    {
        return $this->subject('🔔 New Bank Transfer Request - ChartMasters')
                    ->markdown('emails.admin-bank-transfer-notification');
    }
}