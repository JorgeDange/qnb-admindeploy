<?php

namespace App\Mail;

use App\Models\ImobiliariaPlano;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AssinaturaLiberada extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ImobiliariaPlano $assinatura,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address'),
            subject: 'Assinatura Liberada com Sucesso',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.assinatura-liberada',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
