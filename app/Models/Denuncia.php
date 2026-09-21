<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Denuncia extends Model
{
    use SoftDeletes;

    protected $table = 'denuncias';

    protected $fillable = [
        'imovel_id',
        'imobiliaria_id',
        'cliente_id',
        'autor_nome',
        'autor_email',
        'autor_telefone',
        'motivo',
        'descricao',
        'estado',
        'resolucao',
        'resolvido_por',
        'resolvido_em',
    ];

    protected $casts = [
        'resolvido_em' => 'datetime',
    ];

    public function imovel()
    {
        return $this->belongsTo(Imovel::class);
    }

    public function imobiliaria()
    {
        return $this->belongsTo(Imobiliaria::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function resolvidoPor()
    {
        return $this->belongsTo(Admin::class, 'resolvido_por');
    }

    public function resolver(string $resolucao, int $adminId): void
    {
        $this->update([
            'estado' => 'resolvida',
            'resolucao' => $resolucao,
            'resolvido_por' => $adminId,
            'resolvido_em' => now(),
        ]);
    }
}
