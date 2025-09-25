<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Your Password</title>
</head>
<body style="
font-family:'Apple SD Gothic Neo',sans-serif;
background: #efefef;
            color: #333;padding-top: 70px;padding-bottom: 70px">
<div class="container" style="
max-width: 700px;
            margin: 30px auto;
            padding: 40px;
            background: #fff;
            border-radius: 20px;">
    <center>
        <img src="https://infinitylead.io/logo.png" alt="" width="200">
    </center>

    <img src="" alt="">
    <h1>Forgot your password? Let's get you a new one.</h1>
    <h2>Hello {{ $notifiable->name ?? 'User' }},</h2>
    <p style="text-align: left">You are receiving this email because we received a password reset request for your account.</p>
    <center>
        <a href="{{ $resetUrl }}" class="button" style="
        display: inline-block;
            padding: 20px 30px 15px 30px;
            margin-top: 40px;
            margin-bottom: 40px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;">Reset Password</a>
    </center>
    <p>If you did not request a password reset, no further action is required.</p>
    <div class="footer"

    style="
    margin-top: 30px;
            font-size: 12px;
            color: #999;
            text-align: center;
            ">
        &copy; {{ date('Y') }} Your Company. All rights reserved.
    </div>
</div>
</body>
</html>
