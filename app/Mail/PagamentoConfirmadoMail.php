<?php

namespace App\Mail;

use App\Models\Fatura;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class PagamentoConfirmadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Fatura $fatura,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address'),
            subject: 'Pagamento Confirmado — Fatura ' . $this->fatura->numero,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.faturas.paga',
        );
    }

    public function attachments(): array
    {
        if (!$this->fatura->recibo_pdf_path) {
            return [];
        }

        $caminho = storage_path('app/public/' . $this->fatura->recibo_pdf_path);
        if (!file_exists($caminho)) {
            return [];
        }

        return [
            Attachment::fromPath($caminho)
                ->as('Recibo-' . str_replace('/', '-', $this->fatura->recibo_numero) . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
