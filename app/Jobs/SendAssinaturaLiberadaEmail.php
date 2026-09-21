<?php

namespace App\Jobs;

use App\Mail\AssinaturaLiberada;
use App\Models\ImobiliariaPlano;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendAssinaturaLiberadaEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public ImobiliariaPlano $assinatura
    ) {}

    public function handle(): void
    {
        $email = $this->assinatura->imobiliaria->email;
        Mail::to($email)->send(new AssinaturaLiberada($this->assinatura));
    }
}
