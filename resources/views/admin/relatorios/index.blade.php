@extends('admin.layouts.admin')

@section('title', 'Relatórios - Admin')
@section('pageTitle', 'Relatórios')

@section('content')
<div class="ul-painel-head">
    <h2 class="ul-painel-card-titulo">Relatórios</h2>
</div>

<div class="row">
    <!-- imóveis por estado -->
    <div class="col-md-6">
        <div class="ul-painel-card">
            <div class="ul-painel-head">
                <h3 class="ul-painel-card-titulo">Imóveis por Estado</h3>
            </div>
            @if(isset($imoveisPorEstado) && count($imoveisPorEstado))
                @foreach($imoveisPorEstado as $item)
                <div class="ul-painel-imovel">
                    <div class="ul-painel-imovel-info">
                        <span class="ul-painel-imovel-titulo">{{ ucfirst($item->estado) }}</span>
                        <span class="ul-badge ul-badge--{{ $item->estado }}">{{ ucfirst($item->estado) }}</span>
                    </div>
                    <div class="ul-painel-imovel-acoes">
                        <span class="ul-painel-imovel-stat"><strong>{{ $item->total }}</strong></span>
                    </div>
                </div>
                @endforeach
            @else
                <div class="ul-painel-vazio">
                    <p>Sem dados disponíveis.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- imóveis por tipo -->
    <div class="col-md-6">
        <div class="ul-painel-card">
            <div class="ul-painel-head">
                <h3 class="ul-painel-card-titulo">Imóveis por Tipo</h3>
            </div>
            @if(isset($imoveisPorTipo) && count($imoveisPorTipo))
                @foreach($imoveisPorTipo as $item)
                <div class="ul-painel-imovel">
                    <div class="ul-painel-imovel-info">
                        <span class="ul-painel-imovel-titulo">{{ $item->tipo }}</span>
                    </div>
                    <div class="ul-painel-imovel-acoes">
                        <span class="ul-painel-imovel-stat"><strong>{{ $item->total }}</strong></span>
                    </div>
                </div>
                @endforeach
            @else
                <div class="ul-painel-vazio">
                    <p>Sem dados disponíveis.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <!-- imóveis por província -->
    <div class="col-md-6">
        <div class="ul-painel-card">
            <div class="ul-painel-head">
                <h3 class="ul-painel-card-titulo">Imóveis por Província</h3>
            </div>
            @if(isset($imoveisPorProvincia) && count($imoveisPorProvincia))
                @foreach($imoveisPorProvincia as $item)
                <div class="ul-painel-imovel">
                    <div class="ul-painel-imovel-info">
                        <span class="ul-painel-imovel-titulo">{{ $item->provincia }}</span>
                    </div>
                    <div class="ul-painel-imovel-acoes">
                        <span class="ul-painel-imovel-stat"><strong>{{ $item->total }}</strong></span>
                    </div>
                </div>
                @endforeach
            @else
                <div class="ul-painel-vazio">
                    <p>Sem dados disponíveis.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- imobiliárias por estado -->
    <div class="col-md-6">
        <div class="ul-painel-card">
            <div class="ul-painel-head">
                <h3 class="ul-painel-card-titulo">Imobiliárias por Estado</h3>
            </div>
            @if(isset($imobiliariasPorEstado) && count($imobiliariasPorEstado))
                @foreach($imobiliariasPorEstado as $item)
                <div class="ul-painel-imovel">
                    <div class="ul-painel-imovel-info">
                        <span class="ul-painel-imovel-titulo">{{ ucfirst($item->estado) }}</span>
                        <span class="ul-badge ul-badge--{{ $item->estado === 'aprovada' ? 'aprovado' : ($item->estado === 'pendente' ? 'pendente' : 'cancelado') }}">{{ ucfirst($item->estado) }}</span>
                    </div>
                    <div class="ul-painel-imovel-acoes">
                        <span class="ul-painel-imovel-stat"><strong>{{ $item->total }}</strong></span>
                    </div>
                </div>
                @endforeach
            @else
                <div class="ul-painel-vazio">
                    <p>Sem dados disponíveis.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- mensagens por mês -->
<div class="ul-painel-card">
    <div class="ul-painel-head">
        <h3 class="ul-painel-card-titulo">Mensagens por Mês</h3>
    </div>
    @if(isset($mensagensPorMes) && count($mensagensPorMes))
        @foreach($mensagensPorMes as $item)
        <div class="ul-painel-imovel">
            <div class="ul-painel-imovel-info">
                <span class="ul-painel-imovel-titulo">{{ $item->mes }}</span>
            </div>
            <div class="ul-painel-imovel-acoes">
                <span class="ul-painel-imovel-stat"><strong>{{ $item->total }}</strong></span>
            </div>
        </div>
        @endforeach
    @else
        <div class="ul-painel-vazio">
            <p>Sem dados disponíveis.</p>
        </div>
    @endif
</div>
@endsection
