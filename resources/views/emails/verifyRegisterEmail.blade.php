<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome to InfinityLead</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f8;">
  <center>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f4f6f8; padding: 40px 0;">
      <tr>
        <td align="center">
          <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; padding: 40px; font-family: Arial, sans-serif; color: #333333;">
            <tr>
              <td align="center" style="padding-bottom: 30px;">
                <img src="https://infinitylead.io/logo.png" alt="InfinityLead Logo" width="150" style="display: block;">
              </td>
            </tr>
            <tr>
              <td align="center" style="font-size: 26px; font-weight: bold; color: #1a1a1a; padding-bottom: 10px;">
                Welcome to InfinityLead!
              </td>
            </tr>
            <tr>
              <td style="font-size: 18px; color: #1a73e8; font-weight: bold; padding-bottom: 20px;">
                Hello {{ $user->first_name ?? '' }},
              </td>
            </tr>
            <tr>
              <td style="font-size: 16px; line-height: 1.6; padding-bottom: 15px;">
                Thanks for signing up! Please verify your email to activate your account.
              </td>
            </tr>
            <tr>
              <td align="center" style="padding-bottom: 30px;">
                <a href="{{env('APP_URL')}}verify-email/{{ $user->remember_token }}"
                   style="background-color:#1a73e8;border-radius:6px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:16px;font-weight:bold;line-height:45px;text-align:center;text-decoration:none;width:200px;-webkit-text-size-adjust:none;">
                   Verify Email
                </a>
              </td>
            </tr>
            <tr>
              <td style="font-size: 16px; line-height: 1.6; padding-bottom: 15px;">
                If you didn't create this account, you can ignore this email.
              </td>
            </tr>
            <tr>
              <td style="font-size: 16px; line-height: 1.6; padding-bottom: 2px;">
                Regards,
              </td>
            </tr>
            <tr>
              <td style="font-size: 16px; font-weight: bold; padding-bottom: 30px;">
                The InfinityLead Team
              </td>
            </tr>
            <tr>
              <td align="center" style="font-size: 13px; color: #888888; border-top: 1px solid #e0e0e0; padding-top: 20px;">
                &copy; {{ date('Y') }} INFINITY LEAD LTD. All rights reserved.<br>
                <a href="{{env('APP_URL')}}" style="color: #888888; text-decoration: none;">infinitylead.io</a>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </center>
</body>
</html>
