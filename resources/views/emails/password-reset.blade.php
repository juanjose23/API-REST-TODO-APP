<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Password reset</title>
    <!-- Mantener estilos originales -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body {font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;margin: 0;padding: 0;width: 100%;background-color: #f9fafb;color: #1f2937;-webkit-font-smoothing: antialiased;}
        .container {max-width: 600px;margin: 0 auto;padding: 20px;}
        .email-wrapper {background-color: #ffffff;border-radius: 8px;box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1),0 2px 4px -1px rgba(0,0,0,0.06);overflow: hidden;}
        .email-header {background-color: #4f46e5;padding: 24px;text-align: center;}
        .logo {height: 40px;margin-bottom: 0;}
        .email-body {padding: 32px 24px;}
        h1 {color: #111827;font-size: 24px;font-weight: 700;margin-top: 0;margin-bottom: 16px;}
        p {color: #4b5563;font-size: 16px;line-height: 24px;margin: 16px 0;}
        .button {display: inline-block;background-color: #4f46e5;color: #ffffff;font-weight: 600;font-size: 16px;border-radius: 6px;padding: 12px 24px;margin: 24px 0;text-decoration: none;text-align: center;transition: background-color 0.15s ease;}
        .button:hover {background-color: #4338ca;}
        .email-footer {background-color: #f3f4f6;padding: 24px;text-align: center;font-size: 14px;color: #6b7280;}
        .divider {height: 1px;background-color: #e5e7eb;margin: 24px 0;}
        .security-notice {font-size: 14px;color: #6b7280;padding: 16px 0;border-top: 1px solid #e5e7eb;margin-top: 24px;}
        @media only screen and (max-width: 600px){.container{width:100%!important;padding:10px!important}.email-header{padding:20px!important}.email-body{padding:24px 16px!important}h1{font-size:22px!important}.button{display:block!important;width:100%!important}}
    </style>
</head>
<body>
    <div class="container">
        <div class="email-wrapper">
            <!-- Header -->
            <div class="email-header">
                <img src="https://via.placeholder.com/200x40/4f46e5/ffffff?text=Your+Company" alt="Logo" class="logo">
            </div>

            <!-- Body -->
            <div class="email-body">
                <h1>Hello!</h1>

                <p>We received a request to reset your account password. Don't worry, we're here to help you regain access.</p>

                <p>To create a new password, simply click the button below:</p>

                <div style="text-align: center;">
                    <a href="{{ url( env('FRONT_URL').'/auth/reset-password/'.$token.'?email='.$email) }}" class="button">Reset my password</a>
                </div>

                <p>If the button doesn't work, you can also copy and paste the following link into your browser:</p>
                <p style="background-color: #f3f4f6; padding: 12px; border-radius: 6px; word-break: break-all; font-size: 14px;">
                    {{ url( env('FRONT_URL').'/auth/reset-password/'.$token.'?email='.$email) }}
                </p>

                <div class="divider"></div>

                <p><strong>Didn't request this change?</strong></p>
                <p>If you did not request a password reset, you can ignore this email. Your account is still secure and no changes have been made.</p>

                <div class="security-notice">
                    <p>For security reasons, this link will expire in 60 minutes.</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p> {{ date('Y') }} Your Company. All rights reserved.</p>
                <p>Company address, City, Country</p>
            </div>
        </div>
    </div>
</body>
</html>
