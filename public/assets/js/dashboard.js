// =============================================
// dashboard.js — Mobile Money Dashboard
// =============================================

document.addEventListener('DOMContentLoaded', function() {
  // Toggle sidebar sur mobile
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('sidebar');
  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', function() {
      sidebar.classList.toggle('show');
    });
  }

  // Vérifier que Chart.js est chargé et que les données sont présentes
  if (typeof Chart === 'undefined') {
    console.warn('Chart.js non chargé');
    return;
  }

  // Récupérer les données depuis l'objet global défini dans la vue
  const chartData = window.chartData || { labels: [], dataRetrait: [], dataTransfert: [] };
  const { labels, dataRetrait, dataTransfert } = chartData;

  // Ne pas dessiner si aucune donnée
  if (!labels.length) {
    console.info('Aucune donnée à afficher');
    return;
  }

  const ctx = document.getElementById('chartFrais');
  if (!ctx) return;
  const context = ctx.getContext('2d');

  // Configurer les defaults Chart.js
  Chart.defaults.font.family = 'Inter';
  Chart.defaults.color = '#6B6F7B';

  // Créer le graphique en barres
  new Chart(context, {
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