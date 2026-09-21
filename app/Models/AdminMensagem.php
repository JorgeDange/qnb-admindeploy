<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminMensagem extends Model
{
    protected $table = 'admin_mensagens';
    public $timestamps = false;

    protected $fillable = [
        'thread_id', 'admin_id', 'imobiliaria_id', 'autor_tipo',
        'assunto', 'texto', 'lida', 'lida_em',
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

    public function imobiliaria(): BelongsTo
    {
        return $this->belongsTo(Imobiliaria::class);
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(self::class, 'thread_id');
    }

    public function respostas()
    {
        return $this->hasMany(self::class, 'thread_id')->orderBy('created_at');
    }

    public function ultimaResposta()
    {
        return $this->hasOne(self::class, 'thread_id')->latestOfMany('created_at');
    }

    public function ehPrimeiraDaThread(): bool
    {
        return $this->thread_id === null;
    }

    public function marcarComoLida(): void
    {
        $this->update(['lida' => true, 'lida_em' => now()]);
    }
}
