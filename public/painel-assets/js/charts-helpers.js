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

    /** '#e94e19' + 0.1 → 'rgba(233, 78, 25, 0.1)' */
    function hexToRgba(hex, alpha) {
        var h = String(hex).replace('#', '');
        if (h.length === 3) {
            h = h.split('').map(function (c) { return c + c; }).join('');
        }
        var n = parseInt(h, 16);
        return 'rgba(' + ((n >> 16) & 255) + ', ' + ((n >> 8) & 255) + ', ' + (n & 255) + ', ' + alpha + ')';
    }

    var MESES_PT = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    /** Converte 'Y-m' (ex.: 2026-03) em 'Mar/26'. */
    function labelMes(chave) {
        var partes = String(chave).split('-');
        if (partes.length !== 2) return chave;
        var m = parseInt(partes[1], 10);
        return (MESES_PT[m - 1] || partes[1]) + '/' + String(partes[0]).slice(2);
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

        labelMes: labelMes,

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
        },

        /** Área multi-série (estilo Project Report / Visitors Overview do Duralux).
         *  series: [{ name, data: [num...] }...] — todas com o mesmo comprimento de labels. */
        makeMultiArea: function (el, labels, series, opts) {
            var canvas = resolveCanvas(el);
            if (!canvas || !labels || !labels.length || !series || !series.length) return;
            opts = opts || {};
            var cores = opts.colors || [secondary, success, warning];
            return new Chart(canvas, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: series.map(function (s, i) {
                        return {
                            label: s.name,
                            data: s.data,
                            borderColor: cores[i % cores.length],
                            backgroundColor: hexToRgba(cores[i % cores.length], 0.08),
                            borderWidth: 2,
                            pointRadius: 2,
                            pointBackgroundColor: cores[i % cores.length],
                            fill: true,
                            tension: 0.35
                        };
                    })
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: { grid: { color: grid }, beginAtZero: true }
                    }
                }
            });
        },

        /** Bar agrupado 2+ séries (estilo Website Analytics do Duralux).
         *  series: [{ name, data: [num...] }...] */
        makeGroupedBar: function (el, labels, series, opts) {
            var canvas = resolveCanvas(el);
            if (!canvas || !labels || !labels.length || !series || !series.length) return;
            opts = opts || {};
            var cores = opts.colors || ['#e2e8f0', primary];
            return new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: series.map(function (s, i) {
                        return {
                            label: s.name,
                            data: s.data,
                            backgroundColor: cores[i % cores.length],
                            borderRadius: 5,
                            maxBarThickness: 26
                        };
                    })
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: { grid: { color: grid }, beginAtZero: true }
                    }
                }
            });
        },

        /** Combo bar+line (estilo Payment Records do Duralux).
         *  seriesBar: [{ name, data, color }] ; seriesLine: { name, data, color } */
        makeCombo: function (el, labels, seriesBar, seriesLine, opts) {
            var canvas = resolveCanvas(el);
            if (!canvas || !labels || !labels.length) return;
            opts = opts || {};
            return new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: seriesBar.map(function (s) {
                        return {
                            label: s.name,
                            data: s.data,
                            backgroundColor: s.color,
                            borderRadius: 5,
                            maxBarThickness: 26
                        };
                    }).concat([
                        {
                            label: seriesLine.name,
                            data: seriesLine.data,
                            type: 'line',
                            borderColor: seriesLine.color,
                            backgroundColor: seriesLine.color,
                            borderWidth: 2.5,
                            pointRadius: 3,
                            pointBackgroundColor: seriesLine.color,
                            tension: 0.35,
                            fill: false
                        }
                    ])
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: { grid: { color: grid }, beginAtZero: true }
                    }
                }
            });
        },

        /** Sparkline área (estilo KPIs Duralux) — recebe array de números; sem eixos nem legend. */
        makeSparkline: function (el, values, color) {
            var canvas = resolveCanvas(el);
            if (!canvas || !values || !values.length) return;
            color = color || primary;
            return new Chart(canvas, {
                type: 'line',
                data: {
                    labels: values.map(function (_, i) { return i; }),
                    datasets: [{
                        data: values,
                        borderColor: color,
                        backgroundColor: hexToRgba(color, 0.12),
                        borderWidth: 1.5,
                        pointRadius: 0,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { enabled: false } },
                    scales: {
                        x: { display: false },
                        y: { display: false, beginAtZero: true }
                    }
                }
            });
        }
    };
})();
