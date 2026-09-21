<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Cliente extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = [
        'nome',
        'foto',
        'foto_upload_em',
        'email',
        'telefone',
        'password',
        'email_verificado_em',
        'estado',
        'aceitou_termos_em',
        'aceitou_termos_ip',
        'ultimo_login',
        'ultimo_ip',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verificado_em' => 'datetime',
        'aceitou_termos_em'   => 'datetime',
        'ultimo_login'        => 'datetime',
        'foto_upload_em'      => 'datetime',
        // password is hashed manually in factory/controller
    ];

    public function clienteTokens()
    {
        return $this->hasMany(ClienteToken::class);
    }

    public function scopeAtivos($query)
    {
        return $query->where('estado', 'ativo');
    }

    public function scopeEmailVerificado($query)
    {
        return $query->whereNotNull('email_verificado_em');
    }

    public function estaAtivo(): bool
    {
        return $this->estado === 'ativo';
    }

    public function emailVerificado(): bool
    {
        return !is_null($this->email_verificado_em);
    }

    public function gerarToken(string $tipo, int $minutosValidade = 60): string
    {
        $token = bin2hex(random_bytes(32));

        $this->clienteTokens()->create([
            'token'     => hash('sha256', $token),
            'tipo'      => $tipo,
            'expira_em' => now()->addMinutes($minutosValidade),
        ]);

        return $token;
    }

    public function validarToken(string $token, string $tipo): ?ClienteToken
    {
        return $this->clienteTokens()
            ->where('token', hash('sha256', $token))
            ->where('tipo', $tipo)
            ->whereNull('usado_em')
            ->where('expira_em', '>', now())
            ->first();
    }

    public function favoritos()
    {
        return $this->hasMany(\App\Models\ClienteFavorito::class);
    }

    public function mensagens()
    {
        return $this->hasMany(\App\Models\Mensagem::class);
    }

    public function visitas()
    {
        return $this->hasMany(\App\Models\Visita::class);
    }

    public function pesquisas()
    {
        return $this->hasMany(\App\Models\ClientePesquisa::class);
    }

    public function notificacoes()
    {
        return $this->hasMany(\App\Models\ClienteNotificacao::class);
    }

    public function denuncias()
    {
        return $this->hasMany(\App\Models\Denuncia::class);
    }

    public function avaliacoes()
    {
        return $this->hasMany(\App\Models\Avaliacao::class);
    }

    public function podeUploadFoto(): bool
    {
        if (!$this->foto_upload_em) {
            return true;
        }
        return $this->foto_upload_em->addMonth()->isPast();
    }

    public function diasParaProximoUpload(): int
    {
        if (!$this->foto_upload_em) {
            return 0;
        }
        $proximo = $this->foto_upload_em->addMonth();
        if ($proximo->isPast()) {
            return 0;
        }
        return (int) ceil($proximo->diffInDays(now()));
    }
}
