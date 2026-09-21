<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelatorioAgendado extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'nome', 'tipo', 'frequencia', 'formato', 'destinatarios',
        'filtros', 'ativo', 'ultima_execucao', 'proxima_execucao', 'created_by',
    ];

    protected $casts = [
        'destinatarios' => 'array',
        'filtros' => 'array',
        'ativo' => 'boolean',
        'ultima_execucao' => 'datetime',
        'proxima_execucao' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function criadoPor()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
