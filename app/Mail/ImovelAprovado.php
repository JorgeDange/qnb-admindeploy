<?php

namespace App\Mail;

use App\Models\Imovel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ImovelAprovado extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Imovel $imovel,
        public string $estado,
    ) {}

    public function envelope(): Envelope
    {
        $titulo = $this->estado === 'aprovado'
            ? 'Imóvel Aprovado'
            : 'Imóvel Rejeitado';

        return new Envelope(
            from: config('mail.from.address'),
            subject: $titulo,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.imovel-aprovado',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
