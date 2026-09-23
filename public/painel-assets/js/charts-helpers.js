/* Chart.js helpers partilhados — dashboard + relatórios (QNB-Admin) */
(function () {
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

    /** Aceita canvas, div wrapper com <canvas> filha, ou id string. */
    function resolveCanvas(el) {
        if (!el) return null;
        if (typeof el === 'string') {
            el = document.getElementById(el);
            if (!el) return null;
        }
        if (el.tagName && el.tagName.toUpperCase() === 'CANVAS') return el;
        if (el.querySelector) {
            var child = el.querySelector('canvas');
            if (child) return child;
        }
        return null;
    }

    window.QNBCharts = {
        palette: [success, warning, danger, purple, primary, info, secondary],
        colors: {
            primary: primary,
            secondary: secondary,
            success: success,
            warning: warning,
            danger: danger,
            purple: purple,
            info: info,
            grid: grid,
            texto: texto
        },

        makeDonut: function (el, items) {
            var canvas = resolveCanvas(el);
            if (!canvas || !items || !items.length) return;
            return new Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels: items.map(function (d) { return d.label; }),
                    datasets: [{
                        data: items.map(function (d) { return d.value; }),
                        backgroundColor: window.QNBCharts.palette,
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
        },

        makeBar: function (el, items, horizontal) {
            var canvas = resolveCanvas(el);
            if (!canvas || !items || !items.length) return;
            return new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: items.map(function (d) { return d.label; }),
                    datasets: [{
                        label: 'Total',
                        data: items.map(function (d) { return d.value; }),
                        backgroundColor: horizontal ? window.QNBCharts.palette : primary,
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
                        x: { grid: { display: !!horizontal }, beginAtZero: true },
                        y: { grid: { color: horizontal ? 'transparent' : grid }, beginAtZero: true }
                    }
                }
            });
        },

        makeArea: function (el, items, label, color, rgba) {
            var canvas = resolveCanvas(el);
            if (!canvas || !items || !items.length) return;
            color = color || purple;
            rgba = rgba || 'rgba(139, 92, 246, 0.1)';
            return new Chart(canvas, {
                type: 'line',
                data: {
                    labels: items.map(function (d) { return d.label; }),
                    datasets: [{
                        label: label || 'Total',
                        data: items.map(function (d) { return d.value; }),
                        borderColor: color,
                        backgroundColor: rgba,
                        borderWidth: 2,
                        pointRadius: 3,
                        pointBackgroundColor: color,
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
    };
})();
