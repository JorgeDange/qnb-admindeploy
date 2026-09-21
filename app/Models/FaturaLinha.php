<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaturaLinha extends Model
{
    protected $table = 'fatura_linhas';

    protected $fillable = [
        'fatura_id', 'descricao', 'quantidade', 'valor_unitario', 'subtotal',
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'valor_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function fatura(): BelongsTo
    {
        return $this->belongsTo(Fatura::class);
    }
}
