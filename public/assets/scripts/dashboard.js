document.addEventListener('DOMContentLoaded', function() {
    // Fetch stats from the API
    if (typeof CONFIG === 'undefined' || !CONFIG.baseUrl) return;
    
    fetch(CONFIG.baseUrl + 'dashboard/stats')
        .then(r => r.json())
        .then(data => {
            if (!data.labels || !data.labels.length) return;
            renderBarChart(data);
            renderDoughnutChart(data);
        })
        .catch(err => console.warn('Could not load chart data:', err));

    function renderBarChart(data) {
        const ctx = document.getElementById('barChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Registros',
                    data: data.counts,
                    backgroundColor: createGradients(ctx, data.labels.length),
                    borderColor: 'rgba(125, 95, 255, 0.6)',
                    borderWidth: 1,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: data.labels.length > 5 ? 'y' : 'x',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#12141d',
                        borderColor: 'rgba(125,95,255,0.4)',
                        borderWidth: 1,
                        titleColor: '#7d5fff',
                        bodyColor: '#f1f2f6',
                        cornerRadius: 8,
                        padding: 12,
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(255,255,255,0.03)' },
                        ticks: { color: '#a4b0be', font: { size: 12 } },
                    },
                    y: {
                        grid: { color: 'rgba(255,255,255,0.03)' },
                        ticks: {
                            color: '#a4b0be',
                            font: { size: 12 },
                            precision: 0,
                        },
                        beginAtZero: true,
                    }
                }
            }
        });
    }

    function renderDoughnutChart(data) {
        const ctx = document.getElementById('doughnutChart');
        if (!ctx) return;

        const colors = [
            '#7d5fff', '#18dcff', '#ff6b6b', '#ffd32a',
            '#3ae374', '#ff9ff3', '#48dbfb', '#ff6348'
        ];

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.counts,
                    backgroundColor: colors.slice(0, data.labels.length),
                    borderColor: '#12141d',
                    borderWidth: 3,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#a4b0be',
                            padding: 15,
                            usePointStyle: true,
                            pointStyleWidth: 10,
                            font: { size: 12 },
                        }
                    },
                    tooltip: {
                        backgroundColor: '#12141d',
                        borderColor: 'rgba(125,95,255,0.4)',
                        borderWidth: 1,
                        titleColor: '#7d5fff',
                        bodyColor: '#f1f2f6',
                        cornerRadius: 8,
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                return ` ${context.label}: ${context.parsed} (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    function createGradients(ctx, count) {
        const baseColors = [
            [125, 95, 255],  // purple
            [24, 220, 255],  // cyan
            [255, 107, 107], // red
            [255, 211, 42],  // yellow
            [58, 227, 116],  // green
            [255, 159, 243], // pink
            [72, 219, 251],  // light blue
            [255, 99, 72],   // orange
        ];

        return Array.from({length: count}, (_, i) => {
            const [r, g, b] = baseColors[i % baseColors.length];
            return `rgba(${r}, ${g}, ${b}, 0.7)`;
        });
    }
});
