<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Imovel extends Model
{
    protected $table = 'imoveis';
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'imobiliaria_id', 'referencia', 'titulo', 'tipo', 'finalidade',
        'preco', 'moeda', 'area', 'quartos', 'wc', 'ano_construcao', 'video',
        'provincia', 'municipio', 'bairro', 'endereco', 'descricao',
        'estado', 'disponibilidade', 'destaque', 'visualizacoes', 'contactos',
        'estado_imovel', 'estacionamento',
    ];

    protected function casts(): array
    {
        return [
            'preco' => 'decimal:2',
            'area' => 'decimal:2',
            'quartos' => 'integer',
            'wc' => 'integer',
            'ano_construcao' => 'integer',
            'destaque' => 'boolean',
            'visualizacoes' => 'integer',
            'contactos' => 'integer',
        ];
    }

    public function imobiliaria(): BelongsTo
    {
        return $this->belongsTo(Imobiliaria::class);
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(ImovelFoto::class)->orderBy('ordem');
    }

    public function capaFoto(): ?ImovelFoto
    {
        return $this->fotos()->where('capa', true)->first() ?? $this->fotos()->first();
    }

    public function amenidades(): BelongsToMany
    {
        return $this->belongsToMany(Amenidade::class, 'imovel_amenidade');
    }

    public function canais(): HasMany
    {
        return $this->hasMany(CanalContacto::class);
    }

    public function mensagens(): HasMany
    {
        return $this->hasMany(Mensagem::class);
    }

    public function denuncias(): HasMany
    {
        return $this->hasMany(\App\Models\Denuncia::class);
    }

    public function avaliacoes(): HasMany
    {
        return $this->hasMany(\App\Models\Avaliacao::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('estado', 'aprovado');
    }

    // Accessors para compatibilidade com as views
    public function getTipologiaAttribute(): string
    {
        return $this->tipo;
    }

    public function getDormitoriosAttribute(): ?int
    {
        return $this->quartos;
    }

    public function getBanheirosAttribute(): ?int
    {
        return $this->wc;
    }

    public function getAreaConstruidaAttribute(): ?float
    {
        return $this->area;
    }

    public function getMediaEstrelasAttribute(): ?float
    {
        $media = $this->avaliacoes()
            ->where('estado', 'aprovada')
            ->avg('estrelas');

        return $media ? round($media, 1) : null;
    }

    public function getAvaliacoesCountAttribute(): int
    {
        return $this->avaliacoes()->where('estado', 'aprovada')->count();
    }
}
