<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'nome', 'email', 'password', 'role', 'ativo',
        'ultimo_login', 'ultimo_ip', 'tentativas_login', 'bloqueado_ate',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'ativo' => 'boolean',
            'ultimo_login' => 'datetime',
            'tentativas_login' => 'integer',
            'bloqueado_ate' => 'datetime',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function podeModerar(): bool
    {
        return in_array($this->role, ['super_admin', 'moderador']);
    }

    public function podeGerir(): bool
    {
        return in_array($this->role, ['super_admin', 'comercial']);
    }
}
