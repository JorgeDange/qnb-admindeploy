@extends('admin.layouts.admin')

@section('title', ($admin ? 'Editar' : 'Novo') . ' Administrador — Admin')

@section('content')
<div class="ul-painel-cabecalho">
    <h1 class="ul-painel-titulo">{{ $admin ? 'Editar Administrador' : 'Novo Administrador' }}</h1>
    <a href="{{ route('admin.admins') }}" class="ul-painel-btn ul-painel-btn--cinza">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>

<div class="ul-painel-card" style="max-width:700px;">
    <form action="{{ $admin ? route('admin.admins.atualizar', $admin) : route('admin.admins.salvar') }}" method="POST">
        @csrf
        @if($admin)
            @method('PUT')
        @endif

        <div class="ul-painel-form-campo">
            <label class="ul-painel-form-label">Nome *</label>
            <input type="text" name="nome" class="ul-painel-form-input" value="{{ old('nome', $admin->nome ?? '') }}" required>
            @error('nome')
                <span class="ul-painel-form-erro">{{ $message }}</span>
            @enderror
        </div>

        <div class="ul-painel-form-campo">
            <label class="ul-painel-form-label">Email *</label>
            <input type="email" name="email" class="ul-painel-form-input" value="{{ old('email', $admin->email ?? '') }}" required>
            @error('email')
                <span class="ul-painel-form-erro">{{ $message }}</span>
            @enderror
        </div>

        <div class="ul-painel-form-campo">
            <label class="ul-painel-form-label">{{ $admin ? 'Password (deixe vazio para manter)' : 'Password *' }}</label>
            <input type="password" name="password" class="ul-painel-form-input" {{ $admin ? '' : 'required' }} minlength="8">
            @error('password')
                <span class="ul-painel-form-erro">{{ $message }}</span>
            @enderror
        </div>

        <div class="ul-painel-form-campo">
            <label class="ul-painel-form-label">Confirmar Password {{ $admin ? '' : '*' }}</label>
            <input type="password" name="password_confirmation" class="ul-painel-form-input" minlength="8">
        </div>

        <div class="ul-painel-form-campo">
            <label class="ul-painel-form-label">Papel *</label>
            <select name="role" class="ul-painel-form-input" required>
                @foreach(['super_admin' => 'Super Admin', 'comercial' => 'Comercial', 'moderador' => 'Moderador'] as $val => $label)
                    <option value="{{ $val }}" {{ old('role', $admin->role ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <small style="color:#888;">
                <strong>Super Admin:</strong> acesso total |
                <strong>Comercial:</strong> imobiliárias e imóveis |
                <strong>Moderador:</strong> conteúdo
            </small>
            @error('role')
                <span class="ul-painel-form-erro">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-top:12px;">
            <label class="ul-painel-form-label" style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                <input type="checkbox" name="ativo" value="1" {{ old('ativo', $admin->ativo ?? true) ? 'checked' : '' }}>
                Ativo
            </label>
        </div>

        <div style="margin-top:24px;">
            <button type="submit" class="ul-painel-btn ul-painel-btn--primario">
                <i class="bi bi-check-lg"></i> {{ $admin ? 'Guardar Alterações' : 'Criar Administrador' }}
            </button>
        </div>
    </form>
</div>
@endsection
