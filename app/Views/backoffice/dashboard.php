<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tableau de bord — Mobile Money</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>

<div class="app-shell">

  <!-- SIDEBAR -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
      <div class="brand-mark">MM</div>
      <div class="brand-word">Mobile Money<small>Plateforme de transferts</small></div>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-section-label">Général</div>
      <a href="<?= base_url('backoffice/dashboard') ?>" class="nav-link active"><i class="bi bi-grid-1x2"></i> Tableau de bord</a>
      <a href="#" class="nav-link"><i class="bi bi-table"></i> Portefeuille clients</a>
      <a href="#" class="nav-link"><i class="bi bi-ui-checks"></i> Nouveau dossier</a>
      <div class="nav-section-label">Analyse</div>
      <a href="#" class="nav-link"><i class="bi bi-graph-up-arrow"></i> Performance</a>
      <a href="#" class="nav-link"><i class="bi bi-pie-chart"></i> Répartition</a>
      <a href="#" class="nav-link"><i class="bi bi-file-earmark-text"></i> Rapports</a>
      <div class="nav-section-label">Compte</div>
      <a href="#" class="nav-link"><i class="bi bi-gear"></i> Paramètres</a>
      <a href="#" class="nav-link"><i class="bi bi-box-arrow-right"></i> Déconnexion</a>
    </nav>
    <div class="sidebar-foot">
      © 2026 Mobile Money — v2.4
    </div>
  </aside>

  <!-- MAIN -->
  <div class="main-col">

    <header class="topbar">
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-outline-accent d-lg-none" id="sidebarToggle"><i class="bi bi-list"></i></button>
        <div class="search-box d-none d-md-block">
          <i class="bi bi-search"></i>
          <input type="search" class="form-control" placeholder="Rechercher un client, une opération…">
        </div>
      </div>
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-outline-accent position-relative">
          <i class="bi bi-bell"></i>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background:var(--color-accent); width:8px; height:8px; padding:0;"></span>
        </button>
        <div class="vr d-none d-sm-block"></div>
        <div class="d-flex align-items-center gap-2">
          <div class="avatar-sm">CL</div>
          <div class="d-none d-sm-block">
            <div class="fw-semibold small">Claire Lambert</div>
            <div class="text-faint" style="font-size:.72rem;">Administrateur</div>
          </div>
          <i class="bi bi-chevron-down text-faint small"></i>
        </div>
      </div>
    </header>

    <main class="main-content">

      <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-2">
        <div>
          <div class="eyebrow mb-1">Vue d'ensemble</div>
          <h1 class="h3 font-display mb-0">Bonjour, Claire</h1>
          <p class="text-muted-soft mb-0">Synthèse des opérations du <?= date('d/m/Y', strtotime($date_min)) ?> au <?= date('d/m/Y', strtotime($date_max)) ?>.</p>
        </div>
        <div class="d-flex gap-2">
          <form method="get" class="d-flex gap-2 align-items-center">
            <div>
              <label for="date_min" class="form-label small">Du</label>
              <input type="date" name="date_min" id="date_min" class="form-control form-control-sm" value="<?= $date_min ?>">
            </div>
            <div>
              <label for="date_max" class="form-label small">Au</label>
              <input type="date" name="date_max" id="date_max" class="form-control form-control-sm" value="<?= $date_max ?>">
            </div>
            <button type="submit" class="btn btn-accent btn-sm mt-1"><i class="bi bi-filter"></i> Filtrer</button>
          </form>
        </div>
      </div>

      <!-- KPI Row (Situation Globale) -->
      <?php if (isset($error)): ?>
        <div class="alert alert-warning"><?= $error ?></div>
      <?php else: ?>
      <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
          <div class="card-chic stat-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div class="stat-icon"><i class="bi bi-cash-coin"></i></div>
              <span class="stat-delta up"><i class="bi bi-arrow-up-right"></i> +<?= number_format($total_transactions) ?></span>
            </div>
            <div class="stat-value"><?= number_format($total_frais, 0, ',', ' ') ?> €</div>
            <div class="text-muted-soft small mt-1">Frais totaux (retraits + transferts)</div>
          </div>
        </div>
        <div class="col-6 col-xl-3">
          <div class="card-chic stat-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div class="stat-icon"><i class="bi bi-arrow-left-right"></i></div>
              <span class="stat-delta up"><i class="bi bi-arrow-up-right"></i> <?= number_format($total_transactions) ?></span>
            </div>
            <div class="stat-value"><?= number_format($total_montant, 0, ',', ' ') ?> €</div>
            <div class="text-muted-soft small mt-1">Volume total des transactions</div>
          </div>
        </div>
        <div class="col-6 col-xl-3">
          <div class="card-chic stat-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div class="stat-icon"><i class="bi bi-arrow-up-circle"></i></div>
              <span class="stat-delta up"><i class="bi bi-arrow-up-right"></i> <?= $total_retrait['nb'] ?></span>
            </div>
            <div class="stat-value"><?= number_format($total_retrait['frais'], 0, ',', ' ') ?> €</div>
            <div class="text-muted-soft small mt-1">Frais de retrait</div>
          </div>
        </div>
        <div class="col-6 col-xl-3">
          <div class="card-chic stat-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div class="stat-icon"><i class="bi bi-arrow-right-circle"></i></div>
              <span class="stat-delta up"><i class="bi bi-arrow-up-right"></i> <?= $total_transfert['nb'] ?></span>
            </div>
            <div class="stat-value"><?= number_format($total_transfert['frais'], 0, ',', ' ') ?> €</div>
            <div class="text-muted-soft small mt-1">Frais de transfert</div>
          </div>
        </div>
      </div>

      <!-- Graphique : Évolution des frais quotidiens -->
      <div class="row g-3 mb-4">
        <div class="col-12">
          <div class="card-chic h-100">
            <div class="card-header-chic">
              <div>
                <div class="fw-semibold">Évolution des frais quotidiens</div>
                <div class="text-faint small">Retraits et transferts</div>
              </div>
            </div>
            <div class="card-body-chic">
              <canvas id="chartFrais" height="150"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Liste détaillée (Situation Detail) -->
      <div class="row g-3">
        <div class="col-12">
          <div class="card-chic h-100">
            <div class="card-header-chic">
              <div>
                <div class="fw-semibold">Détail journalier</div>
                <div class="text-faint small">Montants, frais et nombre d'opérations</div>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-chic mb-0">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Retraits (nb / montant / frais)</th>
                    <th>Transferts (nb / montant / frais)</th>
                    <th>Total frais</th>
                    <th>Total transactions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($detail)): ?>
                    <tr><td colspan="5" class="text-center text-muted-soft py-3">Aucune donnée pour cette période.</td></tr>
                  <?php else: ?>
                    <?php foreach ($detail as $row): ?>
                      <tr>
                        <td class="fw-semibold"><?= date('d/m/Y', strtotime($row['date'])) ?></td>
                        <td>
                          <span class="badge-soft info"><?= $row['retrait']['nb'] ?></span>
                          <span class="font-mono"><?= number_format($row['retrait']['montant'], 0, ',', ' ') ?> €</span>
                          <span class="text-faint">/ frais <?= number_format($row['retrait']['frais'], 0, ',', ' ') ?> €</span>
                        </td>
                        <td>
                          <span class="badge-soft accent"><?= $row['transfert']['nb'] ?></span>
                          <span class="font-mono"><?= number_format($row['transfert']['montant'], 0, ',', ' ') ?> €</span>
                          <span class="text-faint">/ frais <?= number_format($row['transfert']['frais'], 0, ',', ' ') ?> €</span>
                        </td>
                        <td class="font-mono fw-semibold"><?= number_format($row['total_frais'], 0, ',', ' ') ?> €</td>
                        <td><?= $row['total_transactions'] ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>

    </main>

    <footer class="text-center text-faint small py-3">
      Mobile Money — Plateforme de gestion des transferts et retraits
    </footer>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
  document.getElementById('sidebarToggle')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('show');
  });

  Chart.defaults.font.family = "Inter";
  Chart.defaults.color = "#6B6F7B";

  // Données injectées par PHP
  const labels = <?= $labels ?: '[]' ?>;
  const dataRetrait = <?= $data_retrait ?: '[]' ?>;
  const dataTransfert = <?= $data_transfert ?: '[]' ?>;
  const dataFrais = <?= $data_frais ?: '[]' ?>;

  // Graphique des frais
  const ctx = document.getElementById('chartFrais').getContext('2d');
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
        },
        {
          label: 'Total frais',
          data: dataFrais,
          type: 'line',
          borderColor: '#12141C',
          borderWidth: 2,
          pointRadius: 2,
          fill: false,
          tension: 0.2,
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
          ticks: { callback: v => v + ' €' }
        }
      }
    }
  });
</script>
</body>
</html>