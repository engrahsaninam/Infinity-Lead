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
                Hello {{ $notifiable->name ?? 'User' }},
              </td>
            </tr>
            <tr>
              <td style="font-size: 16px; line-height: 1.6; padding-bottom: 15px;">
                We're thrilled to have you onboard. Your account has been successfully created, and you're all set to begin exploring the tools that help your business grow.
              </td>
            </tr>
            <tr>
              <td style="font-size: 16px; line-height: 1.6; padding-bottom: 15px;">
                InfinityLead offers you powerful insights, lead generation tools, and automation to help you close more deals — faster.
              </td>
            </tr>
            <tr>
              <td style="font-size: 16px; line-height: 1.6; padding-bottom: 25px;">
                Click below to start exploring your dashboard:
              </td>
            </tr>
            <tr>
              <td align="center" style="padding-bottom: 30px;">
                <!--[if mso]>
                <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word"
                  href="https://infinitylead.io/login"
                  style="height:45px;v-text-anchor:middle;width:200px;" arcsize="10%" stroke="f" fillcolor="#1a73e8">
                  <w:anchorlock/>
                  <center style="color:#ffffff;font-family:sans-serif;font-size:16px;font-weight:bold;">
                    Go to Dashboard
                  </center>
                </v:roundrect>
                <![endif]-->
                <!--[if !mso]><!-- -->
                <a href="https://infinitylead.io/login"
                   style="background-color:#1a73e8;border-radius:6px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:16px;font-weight:bold;line-height:45px;text-align:center;text-decoration:none;width:200px;-webkit-text-size-adjust:none;mso-hide:all;">
                  Go to Dashboard
                </a>
                <!--<![endif]-->
              </td>
            </tr>
            <tr>
              <td style="font-size: 16px; line-height: 1.6; padding-bottom: 15px;">
                If you have any questions, simply reply to this email — we're here to help!
              </td>
            </tr>
            <tr>
              <td style="font-size: 16px; line-height: 1.6; padding-bottom: 15px;">
                Welcome aboard,
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
                <a href="https://infinitylead.io" style="color: #888888; text-decoration: none;">infinitylead.io</a>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </center>
</body>
</html>
