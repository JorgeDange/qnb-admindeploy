@extends('admin.layouts.admin')

@section('title', 'Relatórios - Admin')
@section('pageTitle', 'Relatórios')

@section('content')
<div class="ul-painel-head">
    <h2 class="ul-painel-card-titulo">Relatórios</h2>
</div>

<!-- Gráficos principais -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
    <div class="ul-painel-card">
        <h3 class="ul-painel-card-titulo" style="margin-bottom:16px;">Imóveis por Estado</h3>
        <div id="chartImoveisEstado" class="ul_chart_height"><canvas></canvas></div>
    </div>
    <div class="ul-painel-card">
        <h3 class="ul-painel-card-titulo" style="margin-bottom:16px;">Imobiliárias por Estado</h3>
        <div id="chartImobiliariasEstado" class="ul_chart_height"><canvas></canvas></div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
    <div class="ul-painel-card">
        <h3 class="ul-painel-card-titulo" style="margin-bottom:16px;">Imóveis por Tipo</h3>
        <div id="chartImoveisTipo" class="ul_chart_height"><canvas></canvas></div>
    </div>
    <div class="ul-painel-card">
        <h3 class="ul-painel-card-titulo" style="margin-bottom:16px;">Imóveis por Província</h3>
        <div id="chartImoveisProvincia" class="ul_chart_height"><canvas></canvas></div>
    </div>
</div>

<!-- Project Report — evolução multi-série (Duralux) -->
<div style="display:grid;grid-template-columns:2fr 1fr;grid-auto-flow:dense;gap:20px;margin-bottom:20px;">
    <div class="ul-painel-card">
        <h3 class="ul-painel-card-titulo" style="margin-bottom:16px;">Evolução Mensal — Imóveis, Imobiliárias e Mensagens</h3>
        <div id="chartEvolucao" class="ul_chart_height"><canvas></canvas></div>
    </div>
    <div class="ul-painel-card">
        <h3 class="ul-painel-card-titulo" style="margin-bottom:16px;">Mensagens por Mês</h3>
        <div id="chartMensagensMes" class="ul_chart_height"><canvas></canvas></div>
    </div>
</div>

<!-- Payment Records — combo bar+line (Duralux) + Faturas por Estado -->
<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:20px;">
    <div class="ul-painel-card">
        <h3 class="ul-painel-card-titulo" style="margin-bottom:16px;">Pagamentos por Mês — Confirmados vs Pendentes/Rejeitados</h3>
        <div id="chartPagamentosMes" class="ul_chart_height"><canvas></canvas></div>
    </div>
    <div class="ul-painel-card">
        <h3 class="ul-painel-card-titulo" style="margin-bottom:16px;">Faturas por Estado</h3>
        <div id="chartFaturasEstado" class="ul_chart_height"><canvas></canvas></div>
    </div>
</div>

<!-- Detalhe tabular -->
<div class="row">
    <div class="col-md-6">
        <div class="ul-painel-card">
            <div class="ul-painel-head">
                <h3 class="ul-painel-card-titulo">Imóveis por Estado — detalhe</h3>
            </div>
            @if(isset($estatisticas['imoveis_por_estado']) && count($estatisticas['imoveis_por_estado']))
                @foreach($estatisticas['imoveis_por_estado'] as $item)
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

    <div class="col-md-6">
        <div class="ul-painel-card">
            <div class="ul-painel-head">
                <h3 class="ul-painel-card-titulo">Imobiliárias por Estado — detalhe</h3>
            </div>
            @if(isset($estatisticas['imobiliarias_por_estado']) && count($estatisticas['imobiliarias_por_estado']))
                @foreach($estatisticas['imobiliarias_por_estado'] as $item)
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined' || typeof QNBCharts === 'undefined') return;

    var imoveisEstado = @json(
        collect($estatisticas['imoveis_por_estado'] ?? [])->map(fn($i) => ['label' => ucfirst($i->estado), 'value' => (int) $i->total])->values()
    );
    var imobiliariasEstado = @json(
        collect($estatisticas['imobiliarias_por_estado'] ?? [])->map(fn($i) => ['label' => ucfirst($i->estado), 'value' => (int) $i->total])->values()
    );
    var imoveisTipo = @json(
        collect($estatisticas['imoveis_por_tipo'] ?? [])->map(fn($i) => ['label' => $i->tipo, 'value' => (int) $i->total])->values()
    );
    var imoveisProvincia = @json(
        collect($estatisticas['imoveis_por_provincia'] ?? [])->map(fn($i) => ['label' => $i->provincia, 'value' => (int) $i->total])->values()
    );
    var mensagensMes = @json(
        collect($estatisticas['mensagens_por_mes'] ?? [])->map(fn($i) => ['label' => $i->mes, 'value' => (int) $i->total])->values()
    );
    var meses = @json(collect($estatisticas['imoveis_por_mes'] ?? [])->keys());
    var labelsMes = meses.map(function(m) { return QNBCharts.labelMes(m); });
    var imoveisMesArr = @json(collect($estatisticas['imoveis_por_mes'] ?? [])->values()->map(fn($v) => (int) $v));
    var imobiliariasMesArr = @json(collect($estatisticas['imobiliarias_por_mes'] ?? [])->values()->map(fn($v) => (int) $v));
    var pagConfirmadosMes = @json(collect($estatisticas['pagamentos_confirmados_mes'] ?? [])->values()->map(fn($v) => (int) $v));
    var pagPendentesMes = @json(collect($estatisticas['pagamentos_pendentes_mes'] ?? [])->values()->map(fn($v) => (int) $v));
    var pagRejeitadosMes = @json(collect($estatisticas['pagamentos_rejeitados_mes'] ?? [])->values()->map(fn($v) => (int) $v));
    var faturasEstado = @json(
        collect($estatisticas['faturas_por_estado'] ?? [])->map(fn($i) => ['label' => ucfirst($i->estado), 'value' => (int) $i->total])->values()
    );

    QNBCharts.makeDonut(document.getElementById('chartImoveisEstado'), imoveisEstado);
    QNBCharts.makeDonut(document.getElementById('chartImobiliariasEstado'), imobiliariasEstado);
    QNBCharts.makeBar(document.getElementById('chartImoveisTipo'), imoveisTipo, false);
    QNBCharts.makeBar(document.getElementById('chartImoveisProvincia'), imoveisProvincia, true);
    QNBCharts.makeArea(document.getElementById('chartMensagensMes'), mensagensMes, 'Mensagens');

    // Project Report — evolução multi-série (Duralux)
    QNBCharts.makeMultiArea(document.getElementById('chartEvolucao'), labelsMes, [
        { name: 'Imóveis', data: imoveisMesArr },
        { name: 'Imobiliárias', data: imobiliariasMesArr },
        { name: 'Mensagens', data: mensagensMes.map(function(d) { return d.value; }) }
    ]);

    // Payment Records — combo bar+line (Duralux)
    QNBCharts.makeCombo(document.getElementById('chartPagamentosMes'), labelsMes, [
        { name: 'Pendentes', data: pagPendentesMes, color: '#f59e0b' },
        { name: 'Rejeitados', data: pagRejeitadosMes, color: '#e2e8f0' }
    ], { name: 'Confirmados', data: pagConfirmadosMes, color: '#10b981' });

    // Faturas por Estado — donut (Leads Overview)
    QNBCharts.makeDonut(document.getElementById('chartFaturasEstado'), faturasEstado);
});
</script>
@endpush
