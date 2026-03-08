<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Ferdinand Safaris' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f8fafc;
        }

        .email-wrapper {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            padding: 40px 30px 30px;
            text-align: center;
            position: relative;
        }

        .logo-container {
            background-color: #ffffff;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .logo-icon {
            font-size: 40px;
            color: #059669;
        }

        .email-header h1 {
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 8px 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .email-header .tagline {
            color: #d1fae5;
            font-size: 14px;
            font-weight: 500;
        }

        .email-body {
            padding: 40px 30px;
        }

        .email-body h2 {
            color: #047857;
            font-size: 24px;
            margin-bottom: 16px;
            font-weight: 700;
        }

        .email-body p {
            color: #4b5563;
            margin-bottom: 16px;
            font-size: 16px;
            line-height: 1.7;
        }

        .button {
            display: inline-block;
            padding: 16px 36px;
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            margin: 24px 0;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
        }

        .info-box {
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
            border-left: 4px solid #059669;
            padding: 24px;
            margin: 28px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .info-box h3 {
            color: #047857;
            font-size: 18px;
            margin-bottom: 16px;
            font-weight: 700;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #d1fae5;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #047857;
            font-size: 14px;
        }

        .info-value {
            color: #1f2937;
            font-weight: 500;
            text-align: right;
        }

        .warning-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin: 24px 0;
            border-radius: 8px;
        }

        .warning-box p {
            color: #92400e;
            margin: 0;
            font-weight: 500;
        }

        .email-footer {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            padding: 40px 30px;
            text-align: center;
            color: #9ca3af;
            font-size: 14px;
        }

        .footer-logo {
            font-size: 24px;
            margin-bottom: 12px;
        }

        .email-footer strong {
            color: #ffffff;
            font-size: 18px;
        }

        .email-footer a {
            color: #10b981;
            text-decoration: none;
            font-weight: 500;
        }

        .contact-info {
            margin: 20px 0;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }

        .contact-info p {
            margin: 8px 0;
            color: #d1d5db;
        }

        .social-links {
            margin: 24px 0;
        }

        .social-links a {
            display: inline-block;
            margin: 0 8px;
            width: 40px;
            height: 40px;
            line-height: 40px;
            background-color: rgba(16, 185, 129, 0.1);
            border-radius: 50%;
            color: #10b981;
            font-size: 18px;
        }

        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(255,255,255,0.1), transparent);
            margin: 24px 0;
        }

        @media only screen and (max-width: 600px) {
            .email-wrapper { margin: 0; border-radius: 0; }
            .email-header { padding: 30px 20px 20px; }
            .email-header h1 { font-size: 24px; }
            .email-body { padding: 30px 20px; }
            .info-row { flex-direction: column; }
            .info-value { text-align: left; }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <div class="logo-container">
                <div class="logo-icon">🏔️</div>
            </div>
            <h1>Ferdinand Safaris</h1>
            <p class="tagline">Discover Africa's Wonders</p>
        </div>

        <div class="email-body">
            @yield('content')
        </div>

        <div class="email-footer">
            <div class="footer-logo">🏔️</div>
            <p><strong>Ferdinand Kenya Tours and Safaris</strong></p>
            <p style="color: #d1d5db; margin-top: 8px;">Creating unforgettable African safari experiences</p>

            <div class="contact-info">
                <p><strong style="color: #10b981;">Contact Us</strong></p>
                <p>📧 <a href="mailto:info@ferdinandsafaris.com">info@ferdinandsafaris.com</a></p>
                <p>📞 <a href="tel:+254720968563">+254-720-968563</a></p>
                <p>🌐 <a href="https://www.ferdinandsafaris.com">www.ferdinandsafaris.com</a></p>
                <p>📍 Mombasa, Kenya</p>
            </div>

            <div class="social-links">
                <a href="#" title="Facebook">📘</a>
                <a href="#" title="Instagram">📷</a>
                <a href="#" title="Twitter">🐦</a>
                <a href="#" title="LinkedIn">💼</a>
            </div>

            <div class="divider"></div>

            <p style="font-size: 12px; color: #6b7280;">
                This email was sent by Ferdinand Safaris.<br>
                For inquiries: <a href="mailto:info@ferdinandsafaris.com">info@ferdinandsafaris.com</a>
            </p>

            <p style="font-size: 11px; color: #6b7280; margin-top: 20px;">
                © {{ date('Y') }} Ferdinand Kenya Tours and Safaris. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
