<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portefeuille — Mobile Money</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-mark">MM</div>
            <div class="brand-word">Mobile Money<small>Backoffice</small></div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section-label">Général</div>
            <a href="<?= base_url('backoffice/dashboard') ?>" class="nav-link"><i class="bi bi-grid-1x2"></i> Tableau de bord</a>
            <a href="<?= base_url('backoffice/portefeuille') ?>" class="nav-link active"><i class="bi bi-wallet2"></i> Portefeuille</a>
            <a href="#" class="nav-link"><i class="bi bi-table"></i> Clients</a>
            <a href="#" class="nav-link"><i class="bi bi-ui-checks"></i> Nouveau dossier</a>
            <div class="nav-section-label">Paramètres</div>
            <a href="<?= base_url('backoffice/tarif') ?>" class="nav-link"><i class="bi bi-coin"></i> Tarifs</a>
            <a href="#" class="nav-link"><i class="bi bi-gear"></i> Paramètres</a>
            <a href="#" class="nav-link"><i class="bi bi-box-arrow-right"></i> Déconnexion</a>
        </nav>
        <div class="sidebar-foot">
            © 2026 Mobile Money — v2.4
        </div>
    </aside>

    <div class="main-col">
        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-accent d-lg-none" id="sidebarToggle"><i class="bi bi-list"></i></button>
            </div>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-accent position-relative">
                    <i class="bi bi-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background:var(--color-accent); width:8px; height:8px; padding:0;"></span>
                </button>
                <div class="vr d-none d-sm-block"></div>
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar-sm">AD</div>
                    <div class="d-none d-sm-block">
                        <div class="fw-semibold small">Admin</div>
                        <div class="text-faint" style="font-size:.72rem;">Superviseur</div>
                    </div>
                    <i class="bi bi-chevron-down text-faint small"></i>
                </div>
            </div>
        </header>

        <main class="main-content">
            <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-2">
                <div>
                    <div class="eyebrow mb-1">Gestion des portefeuilles</div>
                    <h1 class="h3 font-display mb-0">État des comptes clients</h1>
                    <p class="text-muted-soft mb-0">Solde des clients à la date sélectionnée</p>
                </div>
                <div class="d-flex gap-2">
                    <form method="get" class="d-flex gap-2 align-items-center">
                        <div>
                            <label for="date" class="form-label small">Date</label>
                            <input type="date" name="date" id="date" class="form-control form-control-sm" value="<?= $date ?>">
                        </div>
                        <button type="submit" class="btn btn-accent btn-sm mt-1"><i class="bi bi-refresh"></i> Mettre à jour</button>
                    </form>
                </div>
            </div>

            <div class="card-chic">
                <div class="card-header-chic">
                    <div>
                        <div class="fw-semibold">Liste des clients</div>
                        <div class="text-faint small"><?= count($portefeuille) ?> client(s) actif(s)</div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-chic mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Numéro</th>
                                <th>Opérateur</th>
                                <th>Date création</th>
                                <th>Solde (Ar)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($portefeuille)): ?>
                                <tr><td colspan="7" class="text-center text-muted-soft py-3">Aucun client trouvé.</td></tr>
                            <?php else: ?>
                                <?php foreach ($portefeuille as $client): ?>
                                    <tr>
                                        <td><?= $client['id'] ?></td>
                                        <td><?= esc($client['nom']) ?></td>
                                        <td><?= esc($client['prenom']) ?></td>
                                        <td><?= esc($client['numero']) ?></td>
                                        <td><?= esc($client['operateur']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($client['date_creation'])) ?></td>
                                        <td class="font-mono fw-semibold <?= ($client['solde'] < 0) ? 'text-danger' : '' ?>">
                                            <?= number_format($client['solde'], 0, ',', ' ') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <footer class="text-center text-faint small py-3">
            Mobile Money — Backoffice
        </footer>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('show');
    });
</script>
</body>
</html>