<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Amenidade extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'icone', 'ativo'];

    protected function casts(): array
    {
        return ['ativo' => 'boolean'];
    }

    public function imoveis(): BelongsToMany
    {
        return $this->belongsToMany(Imovel::class, 'imovel_amenidade');
    }
}
