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

<div class="ul-painel-card" style="margin-bottom:20px;">
    <h3 class="ul-painel-card-titulo" style="margin-bottom:16px;">Mensagens por Mês</h3>
    <div id="chartMensagensMes" class="ul_chart_height"><canvas></canvas></div>
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
    if (typeof Chart === 'undefined') return;

    var primary = '#e94e19';
    var secondary = '#064471';
    var success = '#10b981';
    var warning = '#f59e0b';
    var danger = '#ef4444';
    var purple = '#8b5cf6';
    var info = '#06b6d4';
    var grid = '#f1f5f9';
    var texto = '#64748b';

    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.color = texto;

    var palette = [success, warning, danger, purple, primary, info, secondary];

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

    function makeDonut(el, items) {
        if (!items.length) return;
        new Chart(el, {
            type: 'doughnut',
            data: {
                labels: items.map(d => d.label),
                datasets: [{
                    data: items.map(d => d.value),
                    backgroundColor: palette,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    function makeBar(el, items, horizontal) {
        if (!items.length) return;
        new Chart(el, {
            type: 'bar',
            data: {
                labels: items.map(d => d.label),
                datasets: [{
                    label: 'Total',
                    data: items.map(d => d.value),
                    backgroundColor: horizontal ? palette : primary,
                    borderRadius: 6,
                    maxBarThickness: 40
                }]
            },
            options: {
                indexAxis: horizontal ? 'y' : 'x',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: horizontal }, beginAtZero: true },
                    y: { grid: { color: horizontal ? 'transparent' : grid }, beginAtZero: true }
                }
            }
        });
    }

    function makeArea(el, items) {
        if (!items.length) return;
        new Chart(el, {
            type: 'line',
            data: {
                labels: items.map(d => d.label),
                datasets: [{
                    label: 'Mensagens',
                    data: items.map(d => d.value),
                    borderColor: purple,
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: purple,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { grid: { color: grid }, beginAtZero: true }
                }
            }
        });
    }

    makeDonut(document.getElementById('chartImoveisEstado'), imoveisEstado);
    makeDonut(document.getElementById('chartImobiliariasEstado'), imobiliariasEstado);
    makeBar(document.getElementById('chartImoveisTipo'), imoveisTipo, false);
    makeBar(document.getElementById('chartImoveisProvincia'), imoveisProvincia, true);
    makeArea(document.getElementById('chartMensagensMes'), mensagensMes);
});
</script>
@endpush
