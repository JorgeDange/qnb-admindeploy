<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'admin_id', 'acao', 'modelo', 'modelo_id',
        'detalhes', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'detalhes' => 'array',
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class)->withTrashed();
    }

    public function scopeFiltrar($query, array $filtros)
    {
        return $query
            ->when($filtros['admin_id'] ?? null, fn ($q, $v) => $q->where('admin_id', $v))
            ->when($filtros['acao'] ?? null, fn ($q, $v) => $q->where('acao', 'like', "%$v%"))
            ->when($filtros['data_inicio'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($filtros['data_fim'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '<=', $v));
    }
}
