document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('chartFrais');
    if (!ctx) {
        console.warn('Canvas #chartFrais introuvable.');
        return;
    }

    const chartData = window.chartData || { labels: [], dataRetrait: [], dataTransfert: [] };
    const { labels, dataRetrait, dataTransfert } = chartData;

    if (!labels || !labels.length) {
        console.info('Aucune donnée à afficher.');
        return;
    }

    // Configuration par défaut de Chart.js
    if (typeof Chart !== 'undefined') {
        Chart.defaults.font.family = 'Inter';
        Chart.defaults.color = '#6B6F7B';
    } else {
        console.error('Chart.js non chargé.');
        return;
    }

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Frais retrait',
                    data: dataRetrait,
                    backgroundColor: '#45607C',
                    borderRadius: 4,
                },
                {
                    label: 'Frais transfert',
                    data: dataTransfert,
                    backgroundColor: '#A9814B',
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { usePointStyle: true, boxWidth: 8 }
                }
            },
            scales: {
                x: { grid: { display: false } },
                y: {
                    grid: { color: '#EFECE2' },
                    ticks: { callback: v => v + ' Ar' }
                }
            }
        }
    });
});