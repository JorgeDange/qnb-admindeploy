<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClienteNotificacao extends Model
{
    protected $table = 'cliente_notificacoes';

    protected $fillable = [
        'cliente_id',
        'tipo',
        'titulo',
        'mensagem',
        'url',
        'lida',
        'lida_em',
    ];

    protected $casts = [
        'lida' => 'boolean',
        'lida_em' => 'datetime',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function marcarComoLida(): void
    {
        if (!$this->lida) {
            $this->update(['lida' => true, 'lida_em' => now()]);
        }
    }

    public function scopeNaoLidas($query)
    {
        return $query->where('lida', false);
    }
}
