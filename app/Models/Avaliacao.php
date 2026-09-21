<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Avaliacao extends Model
{
    use SoftDeletes;

    protected $table = 'avaliacoes';

    protected $fillable = [
        'imovel_id',
        'imobiliaria_id',
        'cliente_id',
        'autor_nome',
        'autor_email',
        'estrelas',
        'comentario',
        'estado',
        'motivo_rejeicao',
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
}
