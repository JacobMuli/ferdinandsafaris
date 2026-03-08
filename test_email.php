#!/usr/bin/env php
<?php

/**
 * Email Configuration Test Script
 * 
 * This script tests the email configuration by sending a test email.
 * Run: php test_email.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

echo "=================================\n";
echo "Email Configuration Test\n";
echo "=================================\n\n";

// Display current configuration
echo "Current Mail Configuration:\n";
echo "- Mailer: " . Config::get('mail.default') . "\n";
echo "- Host: " . Config::get('mail.mailers.smtp.host') . "\n";
echo "- Port: " . Config::get('mail.mailers.smtp.port') . "\n";
echo "- Encryption: " . Config::get('mail.mailers.smtp.encryption') . "\n";
echo "- Username: " . Config::get('mail.mailers.smtp.username') . "\n";
echo "- From Address: " . Config::get('mail.from.address') . "\n";
echo "- From Name: " . Config::get('mail.from.name') . "\n\n";

// Prompt for test recipient email
echo "Enter recipient email address for test: ";
$recipient = trim(fgets(STDIN));

if (empty($recipient) || !filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
    echo "❌ Invalid email address provided.\n";
    exit(1);
}

echo "\nSending test email to: $recipient\n";
echo "Please wait...\n\n";

try {
    Mail::raw('This is a test email from Ferdinand Safaris booking platform. If you received this, your email configuration is working correctly!', function ($message) use ($recipient) {
        $message->to($recipient)
                ->subject('Ferdinand Safaris - Email Configuration Test');
    });
    
    echo "✅ Email sent successfully!\n";
    echo "✅ Please check the inbox (and spam folder) of: $recipient\n\n";
    echo "If you don't receive the email within a few minutes, check:\n";
    echo "1. Gmail account credentials are correct\n";
    echo "2. App Password is valid (not regular password)\n";
    echo "3. 2-Step Verification is enabled on Google account\n";
    echo "4. Check spam/junk folder\n";
    
    exit(0);
} catch (Exception $e) {
    echo "❌ Error sending email!\n";
    echo "Error message: " . $e->getMessage() . "\n\n";
    echo "Common issues:\n";
    echo "1. Invalid App Password\n";
    echo "2. Wrong Gmail address\n";
    echo "3. 2-Step Verification not enabled\n";
    echo "4. Network/firewall blocking SMTP connection\n";
    echo "5. Configuration cache needs clearing (run: php artisan config:clear)\n";
    
    exit(1);
}
