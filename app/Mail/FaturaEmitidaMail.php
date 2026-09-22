<?php

namespace App\Mail;

use App\Models\Fatura;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class FaturaEmitidaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Fatura $fatura,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address'),
            subject: 'Fatura ' . $this->fatura->numero . ' Emitida — QNB Imobiliária',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.faturas.emitida',
        );
    }

    public function attachments(): array
    {
        if (!$this->fatura->pdf_path) {
            return [];
        }

        $caminho = storage_path('app/public/' . $this->fatura->pdf_path);
        if (!file_exists($caminho)) {
            return [];
        }

        return [
            Attachment::fromPath($caminho)
                ->as('Fatura-' . str_replace('/', '-', $this->fatura->numero) . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
