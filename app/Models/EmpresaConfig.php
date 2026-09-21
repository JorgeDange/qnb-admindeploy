<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class EmpresaConfig extends Model
{
    protected $table = 'empresa_config';

    protected $fillable = ['chave', 'valor', 'grupo'];

    /**
     * Buscar valor por chave (com cache 1h).
     */
    public static function get(string $chave, ?string $padrao = null): ?string
    {
        return Cache::remember("empresa_config.{$chave}", 3600, function () use ($chave, $padrao) {
            $config = static::where('chave', $chave)->first();
            return $config?->valor ?? $padrao;
        });
    }

    /**
     * Definir valor por chave (cria ou atualiza).
     */
    public static function set(string $chave, ?string $valor, string $grupo = 'geral'): void
    {
        static::updateOrCreate(['chave' => $chave], [
            'valor' => $valor,
            'grupo' => $grupo,
        ]);

        Cache::forget("empresa_config.{$chave}");
    }

    /**
     * Buscar todas as configurações de um grupo.
     */
    public static function getGrupo(string $grupo): array
    {
        return static::where('grupo', $grupo)
            ->pluck('valor', 'chave')
            ->toArray();
    }

    /**
     * Limpar cache de todas as chaves.
     */
    public static function limparCache(): void
    {
        $chaves = static::pluck('chave')->toArray();
        foreach ($chaves as $chave) {
            Cache::forget("empresa_config.{$chave}");
        }
    }
}
