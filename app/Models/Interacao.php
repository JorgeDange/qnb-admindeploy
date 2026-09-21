<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interacao extends Model
{
    protected $table = 'interacoes';

    public $timestamps = false;

    protected $fillable = [
        'cliente_id',
        'imovel_id',
        'imobiliaria_id',
        'tipo',
        'metadados',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'metadados' => 'array',
        'created_at' => 'datetime',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function imovel(): BelongsTo
    {
        return $this->belongsTo(Imovel::class);
    }

    public function imobiliaria(): BelongsTo
    {
        return $this->belongsTo(Imobiliaria::class);
    }
}
