<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string',
            'message' => 'required|string|max:2000',
        ]);

        try {
            // Log the contact form submission
            Log::info('Contact form submitted', [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'subject' => $validated['subject'],
            ]);

            // Send email notification to admin
            Mail::to(config('mail.from.address'))->send(new \App\Mail\ContactFormSubmission($validated));

            // Send confirmation email to customer
            Mail::to($validated['email'])->send(new \App\Mail\ContactConfirmation($validated));

            return back()->with('success', 'Thank you for contacting us! We\'ll respond within 24 hours.');
        } catch (\Exception $e) {
            Log::error('Contact form submission failed', [
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Sorry, something went wrong. Please try again or contact us directly via email.');
        }
    }
}
