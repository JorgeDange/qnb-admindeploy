<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushToken extends Model
{
    protected $table = 'push_tokens';

    protected $fillable = [
        'cliente_id',
        'token',
        'plataforma',
        'user_agent',
        'ativo',
        'ultimo_uso_em',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'ultimo_uso_em' => 'datetime',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function marcarUso(): void
    {
        $this->update(['ultimo_uso_em' => now()]);
    }

    public function desativar(): void
    {
        $this->update(['ativo' => false]);
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }
}
