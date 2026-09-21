@extends('admin.layouts.admin')

@section('title', 'Fatura ' . $fatura->numero . ' — Admin')

@section('content')
<div class="ul-painel-cabecalho">
    <h1 class="ul-painel-titulo">Fatura {{ $fatura->numero }}</h1>
    <div>
        <a href="{{ route('admin.faturas') }}" class="ul-painel-btn ul-painel-btn--cinza">Voltar</a>
        <a href="{{ route('admin.faturas.download', $fatura) }}" class="ul-painel-btn ul-painel-btn--azul">Download PDF</a>
        @if($fatura->isPaga() && $fatura->recibo_numero)
        <a href="{{ route('admin.faturas.recibo', $fatura) }}" class="ul-painel-btn ul-painel-btn--verde">Download Recibo</a>
        @endif
        <form action="{{ route('admin.faturas.reenviar', $fatura) }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="ul-painel-btn ul-painel-btn--amarelo">Reenviar PDF</button>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="ul-painel-card">
            <h3 class="ul-painel-card-titulo">Dados da Fatura</h3>
            <table class="ul-painel-tabela" style="margin:0;">
                <tr><td style="width:200px;font-weight:bold;">Número</td><td>{{ $fatura->numero }}</td></tr>
                <tr><td style="font-weight:bold;">Imobiliária</td><td>{{ $fatura->imobiliaria->nome ?? '—' }}</td></tr>
                <tr><td style="font-weight:bold;">Data Emissão</td><td>{{ $fatura->emitida_em ? $fatura->emitida_em->format('d/m/Y H:i') : '—' }}</td></tr>
                <tr><td style="font-weight:bold;">Moeda</td><td>{{ $fatura->moeda }}</td></tr>
                <tr>
                    <td style="font-weight:bold;">Estado</td>
                    <td>
                        @php
                            $badge = match($fatura->estado) {
                                'paga' => 'ul-badge--sucesso',
                                'pendente' => 'ul-badge--aviso',
                                'aguarda_aprovacao' => 'ul-badge--info',
                                'rejeitada' => 'ul-badge--perigo',
                                'cancelada', 'expirada' => 'ul-badge--cinza',
                                default => 'ul-badge--cinza',
                            };
                        @endphp
                        <span class="ul-badge {{ $badge }}">{{ ucfirst(str_replace('_', ' ', $fatura->estado)) }}</span>
                    </td>
                </tr>
                @if($fatura->recibo_numero)
                <tr><td style="font-weight:bold;">Recibo</td><td>{{ $fatura->recibo_numero }}</td></tr>
                @endif
                @if($fatura->aprovada_em)
                <tr><td style="font-weight:bold;">Aprovada em</td><td>{{ $fatura->aprovada_em->format('d/m/Y H:i') }}</td></tr>
                @endif
                @if($fatura->motivo_rejeicao)
                <tr><td style="font-weight:bold;">Motivo</td><td style="color:#dc3545;">{{ $fatura->motivo_rejeicao }}</td></tr>
                @endif
            </table>
        </div>

        {{-- Comprovativo --}}
        @if($fatura->comprovativo_path)
        <div class="ul-painel-card">
            <h3 class="ul-painel-card-titulo">Comprovativo Submetido</h3>
            <p style="color:#888;font-size:12px;">Enviado em {{ $fatura->comprovativo_enviado_em ? $fatura->comprovativo_enviado_em->format('d/m/Y H:i') : '—' }}</p>
            @if(pathinfo($fatura->comprovativo_path, PATHINFO_EXTENSION) === 'pdf')
            <a href="{{ asset('storage/' . $fatura->comprovativo_path) }}" target="_blank" class="ul-painel-btn ul-painel-btn--azul ul-painel-btn--pequeno">Ver PDF</a>
            @else
            <img src="{{ asset('storage/' . $fatura->comprovativo_path) }}" alt="Comprovativo" style="max-width:100%;max-height:400px;border-radius:8px;border:1px solid #eee;margin-top:10px;">
            @endif
        </div>
        @endif

        <div class="ul-painel-card">
            <h3 class="ul-painel-card-titulo">Linhas da Fatura</h3>
            <table class="ul-painel-tabela">
                <thead>
                    <tr>
                        <th>Descrição</th>
                        <th>Qtd</th>
                        <th>Valor Unit.</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fatura->linhas as $linha)
                    <tr>
                        <td>{{ $linha->descricao }}</td>
                        <td>{{ $linha->quantidade }}</td>
                        <td>{{ number_format($linha->valor_unitario, 2, ',', '.') }}</td>
                        <td><strong>{{ number_format($linha->subtotal, 2, ',', '.') }}</strong></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:#888;">Sem linhas registadas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="ul-painel-card">
            <h3 class="ul-painel-card-titulo">Valores</h3>
            <table class="ul-painel-tabela" style="margin:0;">
                <tr><td style="font-weight:bold;">Subtotal</td><td style="text-align:right;">{{ number_format($fatura->subtotal, 2, ',', '.') }} {{ $fatura->moeda }}</td></tr>
                <tr><td style="font-weight:bold;">IVA</td><td style="text-align:right;">{{ number_format($fatura->iva, 2, ',', '.') }} {{ $fatura->moeda }}</td></tr>
                @if($fatura->desconto > 0)
                <tr><td style="font-weight:bold;">Desconto</td><td style="text-align:right;color:#dc3545;">-{{ number_format($fatura->desconto, 2, ',', '.') }} {{ $fatura->moeda }}</td></tr>
                @endif
                @if($fatura->retencao > 0)
                <tr><td style="font-weight:bold;">Retenção na Fonte</td><td style="text-align:right;color:#dc3545;">-{{ number_format($fatura->retencao, 2, ',', '.') }} {{ $fatura->moeda }}</td></tr>
                @endif
                <tr style="border-top:2px solid #333;"><td style="font-weight:bold;font-size:1.1em;">TOTAL</td><td style="text-align:right;font-weight:bold;font-size:1.1em;">{{ number_format($fatura->total, 2, ',', '.') }} {{ $fatura->moeda }}</td></tr>
            </table>
        </div>

        @if($fatura->nota)
        <div class="ul-painel-card">
            <h3 class="ul-painel-card-titulo">Nota</h3>
            <p>{{ $fatura->nota }}</p>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        {{-- Ações --}}
        @if($fatura->isAguardaAprovacao())
        <div class="ul-painel-card" style="border:2px solid #27ae60;">
            <h3 class="ul-painel-card-titulo">Aprovar Pagamento</h3>
            <form id="form-aprovar-{{ $fatura->id }}" action="{{ route('admin.faturas.aprovar', $fatura) }}" method="POST">
                @csrf
                <button type="button" class="ul-painel-btn ul-painel-btn--sucesso-bloco" onclick="modalConfirmar('Aprovar Pagamento', 'Aprovar este pagamento? O plano será ativado automaticamente.', function(){ document.getElementById('form-aprovar-{{ $fatura->id }}').submit(); })">Aprovar Pagamento</button>
            </form>
        </div>

        <div class="ul-painel-card" style="border:2px solid #dc3545;">
            <h3 class="ul-painel-card-titulo">Rejeitar Pagamento</h3>
            <form id="form-rejeitar-{{ $fatura->id }}" action="{{ route('admin.faturas.rejeitar', $fatura) }}" method="POST">
                @csrf
                <div class="ul-painel-form-campo">
                    <label class="ul-painel-form-label">Motivo da Rejeição</label>
                    <textarea name="motivo" class="ul-painel-form-input" rows="3" placeholder="Descreva o motivo..." required></textarea>
                </div>
                <button type="button" class="ul-painel-btn ul-painel-btn--perigo" style="width:100%;" onclick="modalPerigo('Rejeitar Pagamento', 'Rejeitar este pagamento?', function(){ document.getElementById('form-rejeitar-{{ $fatura->id }}').submit(); })">Rejeitar Pagamento</button>
            </form>
        </div>
        @endif

        @if(!$fatura->isPaga() && !$fatura->isCancelada() && !$fatura->isExpirada())
        <div class="ul-painel-card">
            <h3 class="ul-painel-card-titulo">Cancelar Fatura</h3>
            <form id="form-cancelar-{{ $fatura->id }}" action="{{ route('admin.faturas.cancelar', $fatura) }}" method="POST">
                @csrf
                <button type="button" class="ul-painel-btn ul-painel-btn--cinza" style="width:100%;" onclick="modalPerigo('Cancelar Fatura', 'Tem certeza que deseja cancelar esta fatura?', function(){ document.getElementById('form-cancelar-{{ $fatura->id }}').submit(); })">Cancelar Fatura</button>
            </form>
        </div>
        @endif

        <div class="ul-painel-card">
            <h3 class="ul-painel-card-titulo">Dados da Empresa</h3>
            <table class="ul-painel-tabela" style="margin:0;">
                <tr><td style="font-weight:bold;">Nome</td><td>{{ $fatura->empresa_nome ?? '—' }}</td></tr>
                <tr><td style="font-weight:bold;">NIF</td><td>{{ $fatura->empresa_nif ?? '—' }}</td></tr>
                <tr><td style="font-weight:bold;">Endereço</td><td>{{ $fatura->empresa_endereco ?? '—' }}</td></tr>
                <tr><td style="font-weight:bold;">Telefone</td><td>{{ $fatura->empresa_telefone ?? '—' }}</td></tr>
                <tr><td style="font-weight:bold;">Email</td><td>{{ $fatura->empresa_email ?? '—' }}</td></tr>
            </table>
        </div>

        <div class="ul-painel-card">
            <h3 class="ul-painel-card-titulo">Dados Bancários</h3>
            <table class="ul-painel-tabela" style="margin:0;">
                <tr><td style="font-weight:bold;">Banco</td><td>{{ $fatura->banco_nome ?? '—' }}</td></tr>
                <tr><td style="font-weight:bold;">IBAN</td><td>{{ $fatura->banco_iban ?? '—' }}</td></tr>
                <tr><td style="font-weight:bold;">Titular</td><td>{{ $fatura->banco_titular ?? '—' }}</td></tr>
            </table>
        </div>

        @if($fatura->pdf_path)
        <div class="ul-painel-card">
            <h3 class="ul-painel-card-titulo">PDF</h3>
            <a href="{{ route('admin.faturas.download', $fatura) }}" class="ul-painel-btn ul-painel-btn--azul" style="width:100%;">Download Fatura PDF</a>
            @if($fatura->isPaga() && $fatura->recibo_pdf_path)
            <a href="{{ route('admin.faturas.recibo', $fatura) }}" class="ul-painel-btn ul-painel-btn--verde" style="width:100%;margin-top:8px;">Download Recibo PDF</a>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
