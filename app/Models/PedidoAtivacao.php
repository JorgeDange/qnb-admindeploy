<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoAtivacao extends Model
{
    protected $table = 'pedidos_ativacao';
    use HasFactory;

    protected $fillable = ['imobiliaria_id', 'plano_pretendido', 'mensagem', 'estado'];

    public function imobiliaria(): BelongsTo
    {
        return $this->belongsTo(Imobiliaria::class);
    }
}
