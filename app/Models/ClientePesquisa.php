<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientePesquisa extends Model
{
    use SoftDeletes;

    protected $table = 'cliente_pesquisas';

    protected $fillable = [
        'cliente_id',
        'termo',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
