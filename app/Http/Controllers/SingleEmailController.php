<?php

namespace App\Http\Controllers;

use App\Services\EmailService;
use Illuminate\Http\Request;

class SingleEmailController extends Controller
{
    public function __construct(
        private EmailService $emailService
    ) {}

    public function create()
    {
        return view('emails.single');
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'to_email' => 'required|email',
            'to_name' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'from_email' => 'required|email',
            'from_name' => 'required|string|max:255',
        ]);

        $success = $this->emailService->sendEmail(
            $validated['to_email'],
            $validated['to_name'] ?? '',
            $validated['subject'],
            $validated['body'],
            $validated['from_email'],
            $validated['from_name']
        );

        if ($success) {
            return back()->with('success', 'Email başarıyla gönderildi!');
        }

        return back()->with('error', 'Email gönderilemedi!');
    }
}
