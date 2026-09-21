<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImovelFoto extends Model
{
    use HasFactory;

    protected $fillable = ['imovel_id', 'caminho', 'ordem', 'capa'];

    protected function casts(): array
    {
        return [
            'ordem' => 'integer',
            'capa' => 'boolean',
        ];
    }

    public function imovel(): BelongsTo
    {
        return $this->belongsTo(Imovel::class);
    }
}
