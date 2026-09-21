<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteFavorito extends Model
{
    protected $table = 'cliente_favoritos';

    protected $fillable = ['cliente_id', 'imovel_id'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function imovel()
    {
        return $this->belongsTo(Imovel::class);
    }
}