<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $planoId = $this->route('plano')?->id ?? $this->route('plano');

        return [
            'nome' => 'required|string|max:255|unique:planos,nome,' . $planoId,
            'descricao' => 'nullable|string|max:1000',
            'posts_limite' => 'required|integer|min:1',
            'dias_validade' => 'required|integer|min:1',
            'preco' => 'required|numeric|min:0',
            'moeda' => 'required|in:Kz,USD',
            'ativo' => 'boolean',
            'destaque' => 'boolean',
            'ordem' => 'nullable|integer|min:0',
        ];
    }
}
