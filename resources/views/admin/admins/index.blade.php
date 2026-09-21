@extends('admin.layouts.admin')

@section('title', 'Administradores — Admin')

@section('content')
<div class="ul-painel-cabecalho">
    <h1 class="ul-painel-titulo">Administradores</h1>
    <a href="{{ route('admin.admins.novo') }}" class="ul-painel-btn ul-painel-btn--primario">
        <i class="bi bi-plus-lg"></i> Novo Admin
    </a>
</div>

<div class="ul-painel-tabela-wrap">
    <table class="ul-painel-tabela">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Papel</th>
                <th>Último Login</th>
                <th>Estado</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($admins as $a)
            <tr>
                <td><strong>{{ $a->nome }}</strong></td>
                <td>{{ $a->email }}</td>
                <td>
                    @php
                        $roleBadge = match($a->role) {
                            'super_admin' => 'ul-badge--destaque',
                            'comercial' => 'ul-badge--sucesso',
                            'moderador' => 'ul-badge--aviso',
                            default => 'ul-badge--cinza',
                        };
                    @endphp
                    <span class="ul-badge {{ $roleBadge }}">{{ $a->role }}</span>
                </td>
                <td>{{ $a->ultimo_login?->diffForHumans() ?? 'Nunca' }}</td>
                <td>
                    <form action="{{ route('admin.admins.toggle', $a) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="ul-badge {{ $a->ativo ? 'ul-badge--sucesso' : 'ul-badge--cinza' }}" style="cursor:pointer;border:none;">
                            {{ $a->ativo ? 'Ativo' : 'Inativo' }}
                        </button>
                    </form>
                </td>
                <td>
                    <div class="ul-painel-imovel-acoes" style="display:inline-flex;gap:6px;align-items:center;">
                        <a href="{{ route('admin.admins.editar', $a) }}" class="ul-painel-btn ul-painel-btn--pequeno" title="Editar">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        @if($a->id !== Auth::guard('admin')->id())
                        <form id="admin-del-{{ $a->id }}" action="{{ route('admin.admins.apagar', $a) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="ul-painel-btn ul-painel-btn--perigo ul-painel-btn--pequeno" title="Apagar" onclick="modalPerigo('Apagar Administrador', 'Apagar este administrador?', function(){ document.getElementById('admin-del-{{ $a->id }}').submit(); })">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:40px;color:#888;">Nenhum administrador encontrado.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
