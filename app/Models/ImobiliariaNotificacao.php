<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImobiliariaNotificacao extends Model
{
    protected $table = 'imobiliaria_notificacoes';

    public $timestamps = false;

    protected $fillable = [
        'imobiliaria_id', 'tipo', 'titulo', 'mensagem', 'url',
        'lida', 'lida_em',
    ];

    protected $casts = [
        'lida' => 'boolean',
        'lida_em' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function imobiliaria(): BelongsTo
    {
        return $this->belongsTo(Imobiliaria::class);
    }

    public function marcarComoLida(): void
    {
        $this->update(['lida' => true, 'lida_em' => now()]);
    }

    public static function criar(
        int $imobiliariaId,
        string $tipo,
        string $titulo,
        ?string $mensagem = null,
        ?string $url = null
    ): self {
        return static::create([
            'imobiliaria_id' => $imobiliariaId,
            'tipo' => $tipo,
            'titulo' => $titulo,
            'mensagem' => $mensagem,
            'url' => $url,
        ]);
    }
}
