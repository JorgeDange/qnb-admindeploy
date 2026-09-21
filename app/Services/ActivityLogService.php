<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    public static function log(string $acao, $modelo = null, array $detalhes = []): void
    {
        ActivityLog::create([
            'admin_id' => Auth::guard('admin')->id(),
            'acao' => $acao,
            'modelo' => $modelo ? get_class($modelo) : null,
            'modelo_id' => $modelo?->id,
            'detalhes' => $detalhes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
