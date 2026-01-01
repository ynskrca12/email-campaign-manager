<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class CampaignEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        private string $emailSubject,
        private string $emailBody,
        private string $fromEmail,
        private string $fromName,
        private ?string $recipientName = null,
        private ?string $trackingPixelUrl = null,
        private ?int $recipientId = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->fromEmail, $this->fromName),
            subject: $this->emailSubject,
        );
    }

    public function content(): Content
    {
        // Link'leri tracking URL'leri ile değiştir
        $processedBody = $this->emailBody;

        // Recipient ID'yi link'lere ekle
        if ($this->recipientId) {
            $processedBody = str_replace(
                'href="',
                'href="' . url('/') . '?r=' . $this->recipientId . '&url=',
                $processedBody
            );
        }

        return new Content(
            view: 'emails.campaign',
            with: [
                'body' => $this->emailBody,
                'processedBody' => $processedBody,
                'recipientName' => $this->recipientName,
                'fromName' => $this->fromName,
                'trackingPixelUrl' => $this->trackingPixelUrl,
            ],
        );
    }
}
