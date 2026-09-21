<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pagamento extends Model
{
    protected $fillable = [
        'subscricao_id', 'imobiliaria_id', 'valor', 'moeda', 'metodo',
        'referencia', 'comprovativo', 'estado', 'confirmado_por', 'confirmado_em',
        'motivo_rejeicao',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'confirmado_em' => 'datetime',
    ];

    public function subscricao(): BelongsTo
    {
        return $this->belongsTo(ImobiliariaPlano::class, 'subscricao_id');
    }

    public function imobiliaria(): BelongsTo
    {
        return $this->belongsTo(Imobiliaria::class);
    }

    public function confirmadoPor(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'confirmado_por');
    }

    public function fatura()
    {
        return $this->hasOne(Fatura::class);
    }
}
