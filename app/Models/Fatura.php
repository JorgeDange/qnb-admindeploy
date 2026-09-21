<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fatura extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'numero', 'pagamento_id', 'imobiliaria_id', 'valor', 'moeda',
        'estado', 'tipo',
        'iva', 'total', 'subtotal', 'desconto', 'retencao', 'nota',
        'empresa_nome', 'empresa_nif', 'empresa_endereco',
        'empresa_telefone', 'empresa_email',
        'banco_nome', 'banco_iban', 'banco_titular',
        'pdf_path', 'emitida_em',
        // Comprovativo
        'comprovativo_path', 'comprovativo_enviado_em', 'comprovativo_enviado_por',
        // Aprovação / Rejeição
        'aprovada_por', 'aprovada_em',
        'motivo_rejeicao', 'rejeitada_por', 'rejeitada_em',
        // Recibo
        'recibo_numero', 'recibo_pdf_path', 'recibo_emitido_em',
        // Email timestamps
        'email_emitida_em', 'email_comprovativo_em', 'email_paga_em',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'iva' => 'decimal:2',
        'total' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'desconto' => 'decimal:2',
        'retencao' => 'decimal:2',
        'emitida_em' => 'datetime',
        'comprovativo_enviado_em' => 'datetime',
        'aprovada_em' => 'datetime',
        'rejeitada_em' => 'datetime',
        'recibo_emitido_em' => 'datetime',
        'email_emitida_em' => 'datetime',
        'email_comprovativo_em' => 'datetime',
        'email_paga_em' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function pagamento(): BelongsTo
    {
        return $this->belongsTo(Pagamento::class);
    }

    public function imobiliaria(): BelongsTo
    {
        return $this->belongsTo(Imobiliaria::class);
    }

    public function linhas(): HasMany
    {
        return $this->hasMany(FaturaLinha::class);
    }

    public function emailLogs(): HasMany
    {
        return $this->hasMany(FaturaEmailLog::class);
    }

    public function aprovadaPor()
    {
        return $this->belongsTo(Admin::class, 'aprovada_por');
    }

    public function rejeitadaPor()
    {
        return $this->belongsTo(Admin::class, 'rejeitada_por');
    }

    public function comprovativoEnviadoPor()
    {
        return $this->belongsTo(Imobiliaria::class, 'comprovativo_enviado_por');
    }

    // ── Scopes ─────────────────────────────────────────────────

    public function scopePendentes(Builder $query): Builder
    {
        return $query->where('estado', 'pendente');
    }

    public function scopeAguardaAprovacao(Builder $query): Builder
    {
        return $query->where('estado', 'aguarda_aprovacao');
    }

    public function scopePagas(Builder $query): Builder
    {
        return $query->where('estado', 'paga');
    }

    public function scopeRejeitadas(Builder $query): Builder
    {
        return $query->where('estado', 'rejeitada');
    }

    public function scopeExpiradas(Builder $query): Builder
    {
        return $query->where('estado', 'expirada');
    }

    // ── Helpers ────────────────────────────────────────────────

    public static function proximoNumero(): string
    {
        $ano = date('Y');
        $ultima = static::where('numero', 'like', "FT {$ano}/%")->orderByDesc('numero')->first();
        if ($ultima) {
            $num = (int) substr($ultima->numero, -4) + 1;
        } else {
            $num = 1;
        }
        return "FT {$ano}/" . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public static function proximoNumeroRecibo(): string
    {
        $ano = date('Y');
        $ultima = static::where('recibo_numero', 'like', "REC {$ano}/%")->orderByDesc('recibo_numero')->first();
        if ($ultima) {
            $num = (int) substr($ultima->recibo_numero, -4) + 1;
        } else {
            $num = 1;
        }
        return "REC {$ano}/" . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function isPendente(): bool
    {
        return $this->estado === 'pendente';
    }

    public function isAguardaAprovacao(): bool
    {
        return $this->estado === 'aguarda_aprovacao';
    }

    public function isPaga(): bool
    {
        return $this->estado === 'paga';
    }

    public function isRejeitada(): bool
    {
        return $this->estado === 'rejeitada';
    }

    public function isExpirada(): bool
    {
        return $this->estado === 'expirada';
    }

    public function isCancelada(): bool
    {
        return $this->estado === 'cancelada';
    }
}
