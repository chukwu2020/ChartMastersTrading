{{-- resources/views/emails/admin-bank-transfer-proof.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Bank Transfer Proof Submitted - {{ config('app.name') }}</title>
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
              <p style="color: #0C3A30; font-size: 14px; margin: 10px 0 0 0; font-weight: bold;">PROOF SUBMITTED</p>
            </td>
          </tr>

          <!-- Body -->
          <tr>
            <td style="padding: 40px 30px; font-size: 16px; line-height: 1.6; color: #0C3A30;">
              <p style="margin: 0 0 15px 0; font-size: 18px; font-weight: bold; color: #0C3A30;">
                📎 Bank Transfer Proof Submitted
              </p>

              <p style="margin: 0 0 15px 0;">
                A user has submitted proof of bank transfer for review.
              </p>

              <!-- User Information -->
              <div style="background-color: #f8f9fa; padding: 15px 20px; margin: 15px 0; border-radius: 4px;">
                <p style="margin: 0 0 5px 0; font-weight: bold; color: #0C3A30;">👤 User Information</p>
                <table width="100%" cellpadding="5" cellspacing="0" style="font-size: 14px;">
                  <tr>
                    <td width="30%" style="font-weight: bold;">Name</td>
                    <td>{{ $user->name }}</td>
                  </tr>
                  <tr>
                    <td style="font-weight: bold;">Email</td>
                    <td>{{ $user->email }}</td>
                  </tr>
                  <tr>
                    <td style="font-weight: bold;">Country</td>
                    <td>{{ $user->country ?? 'Not specified' }}</td>
                  </tr>
                </table>
              </div>

              <!-- Deposit Details -->
              <div style="background-color: #e3f2fd; padding: 15px 20px; margin: 15px 0; border-radius: 4px; border-left: 4px solid #2196f3;">
                <p style="margin: 0 0 5px 0; font-weight: bold; color: #0C3A30;">💰 Deposit Details</p>
                <table width="100%" cellpadding="5" cellspacing="0" style="font-size: 14px;">
                  <tr>
                    <td width="30%" style="font-weight: bold;">Amount</td>
                    <td><strong>${{ number_format($deposit->amount_deposited, 2) }}</strong></td>
                  </tr>
                  <tr>
                    <td style="font-weight: bold;">Reference Code</td>
                    <td><strong style="background-color: #ffffff; padding: 2px 10px; border-radius: 4px; letter-spacing: 1px;">{{ $deposit->bank_details['reference_code'] ?? 'N/A' }}</strong></td>
                  </tr>
                  <tr>
                    <td style="font-weight: bold;">Country</td>
                    <td>{{ $deposit->bank_details['country'] ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td style="font-weight: bold;">Bank</td>
                    <td>{{ $deposit->bank_details['bank_name'] ?? 'N/A' }}</td>
                  </tr>
                </table>
              </div>

              <!-- Action Required -->
              <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px 20px; margin: 20px 0; border-radius: 4px;">
                <p style="margin: 0 0 8px 0; font-weight: bold; color: #856404;">🎯 Actions Required</p>
                <ul style="margin: 0; padding-left: 20px; color: #856404; font-size: 14px;">
                  <li style="margin-bottom: 5px;">Review the submitted proof</li>
                  <li style="margin-bottom: 5px;">Verify the bank transfer</li>
                  <li style="margin-bottom: 5px;">Approve or reject the deposit</li>
                  <li>Update the user's balance</li>
                </ul>
              </div>

              <div style="text-align: center; margin: 25px 0;">
                <a href="{{ $adminUrl ?? route('admin.deposits.pending') }}" style="display: inline-block; background-color: #0C3A30; color: #ffffff; padding: 14px 28px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 16px;">
                  ✅ Review Now
                </a>
              </div>

              <p style="margin: 0 0 20px 0; font-size: 14px; color: #6c757d;">
                This requires your immediate attention.
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