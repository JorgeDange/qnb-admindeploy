<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visita extends Model
{
    use SoftDeletes;

    protected $table = 'visitas';

    protected $fillable = [
        'imovel_id',
        'imobiliaria_id',
        'cliente_id',
        'cliente_nome',
        'cliente_email',
        'cliente_telefone',
        'data_visita',
        'estado',
        'observacoes',
        'feedback',
    ];

    protected $casts = [
        'data_visita' => 'datetime',
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
