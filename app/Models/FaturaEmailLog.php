<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaturaEmailLog extends Model
{
    protected $table = 'fatura_emails_log';

    protected $fillable = [
        'fatura_id', 'tipo_email', 'destinatario', 'enviado_em', 'metadata',
    ];

    protected $casts = [
        'enviado_em' => 'datetime',
        'metadata' => 'array',
    ];

    public function fatura(): BelongsTo
    {
        return $this->belongsTo(Fatura::class);
    }
}
