<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Imobiliaria extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'nome', 'foto', 'nif', 'email', 'telefone', 'provincia', 'municipio',
        'password', 'estado', 'aprovado_em',
        'motivo_suspensao', 'suspensa_ate', 'suspensa_por',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'aprovado_em' => 'datetime',
            'suspensa_ate' => 'datetime',
        ];
    }

    public function plano(): HasOne
    {
        return $this->hasOne(ImobiliariaPlano::class);
    }

    public function pedidosAtivacao(): HasMany
    {
        return $this->hasMany(PedidoAtivacao::class);
    }

    public function imoveis(): HasMany
    {
        return $this->hasMany(Imovel::class);
    }

    public function mensagens(): HasMany
    {
        return $this->hasMany(Mensagem::class);
    }

    public function canais(): HasMany
    {
        return $this->hasMany(CanalContacto::class);
    }

    public function visitas(): HasMany
    {
        return $this->hasMany(Visita::class);
    }

    public function avaliacoes(): HasMany
    {
        return $this->hasMany(Avaliacao::class);
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class);
    }

    /* Plano ativo, considerando expiração por data ou posts esgotados. */
    public function getPlanoAtivo(): ?ImobiliariaPlano
    {
        $p = $this->plano()->where('ativo', true)->first();
        if (!$p) {
            return null;
        }
        $expirado = $p->data_expiracao !== null && $p->data_expiracao->isPast();
        if ($expirado || $p->posts_usados >= $p->plano->posts_limite) {
            $p->update(['ativo' => false]);
            return null;
        }
        return $p;
    }

    public function estadoAtual(): string
    {
        if ($this->estado === 'aprovada' && $this->getPlanoAtivo()) {
            return 'ativo';
        }
        return $this->estado;
    }
}
