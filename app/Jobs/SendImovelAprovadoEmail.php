<?php

namespace App\Jobs;

use App\Mail\ImovelAprovado;
use App\Models\Imovel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendImovelAprovadoEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Imovel $imovel,
        public string $estado
    ) {}

    public function handle(): void
    {
        $email = $this->imovel->imobiliaria->email;
        Mail::to($email)->send(new ImovelAprovado($this->imovel, $this->estado));
    }
}
