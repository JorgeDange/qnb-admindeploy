<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImobiliariaPlano extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'imobiliaria_plano';

    protected $fillable = [
        'imobiliaria_id', 'plano_id', 'posts_usados',
        'data_inicio', 'data_expiracao', 'ativo',
        'estado', 'renovacao_automatica',
    ];

    protected function casts(): array
    {
        return [
            'posts_usados' => 'integer',
            'data_inicio' => 'date',
            'data_expiracao' => 'date',
            'ativo' => 'boolean',
            'renovacao_automatica' => 'boolean',
        ];
    }

    public function imobiliaria(): BelongsTo
    {
        return $this->belongsTo(Imobiliaria::class);
    }

    public function plano(): BelongsTo
    {
        return $this->belongsTo(Plano::class);
    }

    public function diasRestantes(): int
    {
        if (!$this->data_expiracao) {
            return 0;
        }
        return max(0, (int) now()->diffInDays($this->data_expiracao, false));
    }

    public function expirada(): bool
    {
        return $this->data_expiracao && $this->data_expiracao->isPast();
    }

    public function renovar(): void
    {
        $base = $this->data_expiracao ?? now();

        $this->update([
            'data_expiracao' => $base->copy()->addDays($this->plano->dias_validade),
            'posts_usados' => 0,
        ]);
    }
}
