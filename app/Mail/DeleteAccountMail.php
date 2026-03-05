<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DeleteAccountMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $code,
        public string $name
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Confirmar eliminación de cuenta - AgroFinanzas');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.delete-account');
    }
}