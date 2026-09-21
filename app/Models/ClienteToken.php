<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteToken extends Model
{
    protected $table = 'cliente_tokens';

    protected $fillable = ['cliente_id', 'token', 'tipo', 'expira_em', 'usado_em'];

    protected $casts = [
        'expira_em' => 'datetime',
        'usado_em'  => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
