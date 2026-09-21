<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['chave', 'valor', 'tipo', 'grupo', 'descricao'];
    public $timestamps = false;

    public static function get(string $chave, mixed $default = null): mixed
    {
        $all = Cache::rememberForever('settings', fn () => static::pluck('valor', 'chave')->toArray());

        if (!isset($all[$chave])) {
            return $default;
        }

        $setting = static::where('chave', $chave)->first();
        $valor = $all[$chave];

        return match ($setting?->tipo) {
            'integer' => (int) $valor,
            'boolean' => filter_var($valor, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($valor, true),
            default => $valor,
        };
    }

    public static function set(string $chave, mixed $valor): void
    {
        $tipo = is_bool($valor)
            ? 'boolean'
            : (is_int($valor)
                ? 'integer'
                : (is_array($valor)
                    ? 'json'
                    : 'string'));

        static::updateOrCreate(
            ['chave' => $chave],
            [
                'valor' => is_array($valor) ? json_encode($valor) : (string) $valor,
                'tipo' => $tipo,
            ]
        );

        Cache::forget('settings');
    }

    public static function grupo(string $grupo): \Illuminate\Support\Collection
    {
        return static::where('grupo', $grupo)->get();
    }
}
