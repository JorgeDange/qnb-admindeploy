<?php

namespace App\Mail;

use App\Models\Fatura;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ComprovativoRejeitadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Fatura $fatura,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address'),
            subject: 'Comprovativo Rejeitado — Fatura ' . $this->fatura->numero,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.faturas.rejeitada',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
