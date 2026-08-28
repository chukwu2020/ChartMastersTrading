{{-- resources/views/emails/bank-transfer-details.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Bank Transfer Details - {{ config('app.name') }}</title>
</head>

<body style="margin: 0; padding: 0; background-color: #f2f2f2; font-family: Helvetica, Arial, sans-serif; color: #0C3A30;">

  <table width="100%" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2" style="padding: 30px 0;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">

          <!-- Header -->
          <tr>
            <td style="background-color: #8bc905; padding: 30px 20px; text-align: center;">
              <img src="https://res.cloudinary.com/dswwq3xks/image/upload/v1774272450/chartmasterlogo1_z25kgc.png" alt="Chartmasters Circle" style="height: 80px; width: auto; display: block; margin: 0 auto;">
            </td>
          </tr>

          <!-- Body -->
          <tr>
            <td style="padding: 40px 30px; font-size: 16px; line-height: 1.6; color: #0C3A30;">
              <p style="margin: 0 0 15px 0;">
                Hello {{ $user->name }},
              </p>

              <p style="margin: 0 0 15px 0; font-size: 18px; font-weight: bold; color: #0C3A30;">
                🔷 Bank Transfer Details Generated
              </p>

              <p style="margin: 0 0 15px 0;">
                You have successfully generated bank transfer details for your deposit of <strong>${{ number_format($amount, 2) }}</strong>.
                Please transfer the exact amount to the account details below:
              </p>

              <!-- Bank Details Box -->
              <div style="background-color: #f8f9fa; border-left: 4px solid #8bc905; padding: 20px; margin: 25px 0; border-radius: 4px;">
                <table width="100%" cellpadding="8" cellspacing="0" style="font-size: 15px;">
                  <tr>
                    <td width="40%" style="font-weight: bold; color: #0C3A30;">Bank Name</td>
                    <td width="60%">{{ $bankTransfer->bank_name }}</td>
                  </tr>
                  <tr>
                    <td style="font-weight: bold; color: #0C3A30;">Account Name</td>
                    <td>{{ $bankTransfer->account_name }}</td>
                  </tr>
                  <tr>
                    <td style="font-weight: bold; color: #0C3A30;">Account Number</td>
                    <td><strong style="color: #8bc905; font-size: 18px;">{{ $bankTransfer->account_number }}</strong></td>
                  </tr>
                  <tr>
                    <td style="font-weight: bold; color: #0C3A30;">Reference Code</td>
                    <td><strong style="background-color: #e8f5e9; padding: 4px 12px; border-radius: 4px; font-size: 16px; letter-spacing: 2px; color: #0C3A30;">{{ $bankTransfer->reference_code }}</strong></td>
                  </tr>
                  @if($bankTransfer->swift_code)
                  <tr>
                    <td style="font-weight: bold; color: #0C3A30;">SWIFT Code</td>
                    <td>{{ $bankTransfer->swift_code }}</td>
                  </tr>
                  @endif
                  @if($bankTransfer->routing_number)
                  <tr>
                    <td style="font-weight: bold; color: #0C3A30;">Routing Number</td>
                    <td>{{ $bankTransfer->routing_number }}</td>
                  </tr>
                  @endif
                  @if($bankTransfer->iban)
                  <tr>
                    <td style="font-weight: bold; color: #0C3A30;">IBAN</td>
                    <td>{{ $bankTransfer->iban }}</td>
                  </tr>
                  @endif
                  <tr>
                    <td style="font-weight: bold; color: #0C3A30;">Country</td>
                    <td>{{ $bankTransfer->country }}</td>
                  </tr>
                </table>
              </div>

              <!-- Important Notes -->
              <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px 20px; margin: 20px 0; border-radius: 4px;">
                <p style="margin: 0 0 8px 0; font-weight: bold; color: #856404;">⚠️ Important Notes</p>
                <ul style="margin: 0; padding-left: 20px; color: #856404; font-size: 14px;">
                  <li style="margin-bottom: 5px;"><strong>Amount:</strong> ${{ number_format($amount, 2) }} (Exact amount required)</li>
                  <li style="margin-bottom: 5px;"><strong>Reference:</strong> Must include <strong>{{ $bankTransfer->reference_code }}</strong> in the transfer description</li>
                  <li style="margin-bottom: 5px;"><strong>Expires:</strong> {{ $bankTransfer->expires_at->format('F d, Y H:i') }}</li>
                  <li><strong>Processing:</strong> 1-2 business days after confirmation</li>
                </ul>
              </div>

              <p style="margin: 20px 0 10px 0;">
                After making the transfer, please upload your proof of payment:
              </p>

              <div style="text-align: center; margin: 25px 0;">
                <a href="{{ route('user.deposit') }}" style="display: inline-block; background-color: #0C3A30; color: #ffffff; padding: 14px 28px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 16px;">
                  📤 Upload Proof Now
                </a>
              </div>

              <p style="margin: 0 0 20px 0; font-size: 14px; color: #6c757d;">
                If you have any questions, please contact our support team at support@chartmasters.com
              </p>

              <p style="margin-top: 40px; font-size: 14px;">
                Regards,<br>
                <strong>The Chartmasters Circle Team</strong>
              </p>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="background-color: #f2f2f2; text-align: center; padding: 20px; font-size: 12px; color: #0C3A30;">
              &copy; {{ date('Y') }} Chartmasters Circle. All rights reserved.
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</body>

</html>