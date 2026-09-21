<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacao extends Model
{
    protected $table = 'notificacoes';

    public $timestamps = false;

    protected $fillable = [
        'admin_id', 'tipo', 'titulo', 'mensagem', 'url',
        'lida', 'lida_em',
    ];

    protected $casts = [
        'lida' => 'boolean',
        'lida_em' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function marcarComoLida(): void
    {
        $this->update(['lida' => true, 'lida_em' => now()]);
    }
}
