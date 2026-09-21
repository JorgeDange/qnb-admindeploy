@extends('admin.layouts.admin')

@section('title', 'Dashboard - Admin')

@section('content')

<div class="ul-painel-cabecalho">
    <h1 class="ul-painel-titulo">Dashboard</h1>
    <div style="display:flex;gap:8px;">
        @foreach([7 => '7 dias', 30 => '30 dias', 90 => '90 dias', 365 => '1 ano'] as $dias => $label)
            <a href="{{ route('admin.dashboard', ['periodo' => $dias]) }}" class="ul-painel-btn ul-painel-btn--pequeno {{ $periodo == $dias ? 'ul-painel-btn--primario' : 'ul-painel-btn--cinza' }}">{{ $label }}</a>
        @endforeach
    </div>
</div>

<!-- KPIs -->
<div class="ul-painel-stats">
    <div class="ul-painel-stat">
        <span class="ul-painel-stat-numero">{{ $stats['total_imobiliarias'] }}</span>
        <span class="ul-painel-stat-rotulo">Imobiliárias</span>
        @if($stats['imobiliarias_novas'] > 0)
            <small class="ul-painel-texto-sucesso">+{{ $stats['imobiliarias_novas'] }} novas</small>
        @endif
    </div>
    <div class="ul-painel-stat">
        <span class="ul-painel-stat-numero">{{ $stats['total_imoveis'] }}</span>
        <span class="ul-painel-stat-rotulo">Imóveis</span>
        @if($stats['imoveis_novos'] > 0)
            <small class="ul-painel-texto-sucesso">+{{ $stats['imoveis_novos'] }} novos</small>
        @endif
    </div>
    <a href="{{ route('admin.imobiliarias', ['estado' => 'pendente']) }}" class="text-decoration-none">
        <div class="ul-painel-stat" {{ $stats['imobiliarias_pendentes'] > 0 ? 'style="border-left:4px solid #f59e0b;"' : '' }}>
            <span class="ul-painel-stat-numero">{{ $stats['imobiliarias_pendentes'] }}</span>
            <span class="ul-painel-stat-rotulo">Pendentes</span>
        </div>
    </a>
    <a href="{{ route('admin.imoveis', ['estado' => 'pendente']) }}" class="text-decoration-none">
        <div class="ul-painel-stat" {{ $stats['imoveis_pendentes'] > 0 ? 'style="border-left:4px solid #f59e0b;"' : '' }}>
            <span class="ul-painel-stat-numero">{{ $stats['imoveis_pendentes'] }}</span>
            <span class="ul-painel-stat-rotulo">Imóveis Pendentes</span>
        </div>
    </a>
    <a href="{{ route('admin.pedidos', ['estado' => 'novo']) }}" class="text-decoration-none">
        <div class="ul-painel-stat" {{ $stats['pedidos_novos'] > 0 ? 'style="border-left:4px solid #ef4444;"' : '' }}>
            <span class="ul-painel-stat-numero">{{ $stats['pedidos_novos'] }}</span>
            <span class="ul-painel-stat-rotulo">Pedidos Novos</span>
        </div>
    </a>
</div>

<!-- Gráficos -->
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
        <h3 class="ul-painel-card-titulo" style="margin-bottom:16px;">Imóveis por Província</h3>
        <div id="chartImoveisProvincia" class="ul_chart_height"><canvas></canvas></div>
    </div>
    <div class="ul-painel-card">
        <h3 class="ul-painel-card-titulo" style="margin-bottom:16px;">Mensagens por Mês</h3>
        <div id="chartMensagensMes" class="ul_chart_height"><canvas></canvas></div>
    </div>
</div>

<!-- Listas recentes -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
    <!-- Imobiliárias recentes -->
    <div class="ul-painel-card">
        <div class="ul-painel-cabecalho" style="margin-bottom:12px;">
            <h3 class="ul-painel-card-titulo">Últimas Imobiliárias</h3>
            <a href="{{ route('admin.imobiliarias') }}" class="ul-painel-btn ul-painel-btn--pequeno">Ver Todas</a>
        </div>
        @forelse($recentes['imobiliarias'] as $imob)
        <div class="ul-painel-lista-item">
            <div class="ul-painel-lista-info">
                <strong>{{ $imob->nome }}</strong>
                <small>{{ $imob->email }}</small>
            </div>
            <span class="ul-badge ul-badge--{{ $imob->estado }}">{{ ucfirst($imob->estado) }}</span>
            <a href="{{ route('admin.imobiliarias.show', $imob) }}" class="ul-painel-btn ul-painel-btn--pequeno">Ver</a>
        </div>
        @empty
        <p style="color:#888;text-align:center;padding:20px;">Nenhuma imobiliária.</p>
        @endforelse
    </div>

    <!-- Imóveis recentes -->
    <div class="ul-painel-card">
        <div class="ul-painel-cabecalho" style="margin-bottom:12px;">
            <h3 class="ul-painel-card-titulo">Últimos Imóveis</h3>
            <a href="{{ route('admin.imoveis') }}" class="ul-painel-btn ul-painel-btn--pequeno">Ver Todos</a>
        </div>
        @forelse($recentes['imoveis'] as $imovel)
        <div class="ul-painel-lista-item">
            <div class="ul-painel-lista-info">
                <strong>{{ $imovel->titulo }}</strong>
                <small>{{ number_format($imovel->preco, 0, ',', '.') }} {{ $imovel->moeda }}</small>
            </div>
            <span class="ul-badge ul-badge--{{ $imovel->estado }}">{{ ucfirst($imovel->estado) }}</span>
            <a href="{{ route('admin.imoveis.show', $imovel) }}" class="ul-painel-btn ul-painel-btn--pequeno">Ver</a>
        </div>
        @empty
        <p style="color:#888;text-align:center;padding:20px;">Nenhum imóvel.</p>
        @endforelse
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/chart.umd.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
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

    // Imóveis por Estado (Donut)
    var imoveisEstadoData = @json(
        collect($chartData['imoveis_por_estado'])->map(function($total, $estado) {
            return ['label' => ucfirst($estado), 'value' => $total];
        })->values()->toArray()
    );

    if (imoveisEstadoData.length > 0) {
        new Chart(document.getElementById('chartImoveisEstado'), {
            type: 'doughnut',
            data: {
                labels: imoveisEstadoData.map(function(d) { return d.label; }),
                datasets: [{
                    data: imoveisEstadoData.map(function(d) { return d.value; }),
                    backgroundColor: [success, warning, danger, purple, primary, info, secondary],
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

    // Imobiliárias por Estado (Donut)
    var imobEstadoData = @json(
        collect($chartData['imobiliarias_por_estado'])->map(function($total, $estado) {
            return ['label' => ucfirst($estado), 'value' => $total];
        })->values()->toArray()
    );

    if (imobEstadoData.length > 0) {
        new Chart(document.getElementById('chartImobiliariasEstado'), {
            type: 'doughnut',
            data: {
                labels: imobEstadoData.map(function(d) { return d.label; }),
                datasets: [{
                    data: imobEstadoData.map(function(d) { return d.value; }),
                    backgroundColor: [success, warning, danger, purple, primary, info, secondary],
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

    // Imóveis por Província (Barra)
    var imoveisProvData = @json(
        collect($chartData['imoveis_por_provincia'])->map(function($total, $provincia) {
            return ['y' => $provincia, 'a' => $total];
        })->values()->toArray()
    );

    if (imoveisProvData.length > 0) {
        new Chart(document.getElementById('chartImoveisProvincia'), {
            type: 'bar',
            data: {
                labels: imoveisProvData.map(function(d) { return d.y; }),
                datasets: [{
                    label: 'Imóveis',
                    data: imoveisProvData.map(function(d) { return d.a; }),
                    backgroundColor: primary,
                    borderRadius: 6,
                    maxBarThickness: 40
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

    // Mensagens por Mês (Linha)
    var msgsData = @json(
        collect($chartData['mensagens_por_mes'])->map(function($total, $mes) {
            return ['y' => $mes, 'item1' => $total];
        })->values()->toArray()
    );

    if (msgsData.length > 0) {
        new Chart(document.getElementById('chartMensagensMes'), {
            type: 'line',
            data: {
                labels: msgsData.map(function(d) { return d.y; }),
                datasets: [{
                    label: 'Mensagens',
                    data: msgsData.map(function(d) { return d.item1; }),
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
});
</script>
@endpush
