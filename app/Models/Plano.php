<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plano extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['nome', 'descricao', 'posts_limite', 'dias_validade', 'preco', 'moeda', 'ativo', 'destaque', 'ordem'];

    protected function casts(): array
    {
        return [
            'posts_limite' => 'integer',
            'dias_validade' => 'integer',
            'preco' => 'decimal:2',
            'ativo' => 'boolean',
            'destaque' => 'boolean',
            'ordem' => 'integer',
        ];
    }

    public function imobiliarias(): HasMany
    {
        return $this->hasMany(ImobiliariaPlano::class);
    }
}
