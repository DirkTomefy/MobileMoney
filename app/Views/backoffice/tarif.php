<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des tarifs — Mobile Money</title>
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
            <a href="<?= base_url('backoffice/dashboard') ?>" class="nav-link"><i class="bi bi-grid-1x2"></i> Tableau de bord</a>
            <a href="#" class="nav-link"><i class="bi bi-table"></i> Portefeuille clients</a>
            <a href="#" class="nav-link"><i class="bi bi-ui-checks"></i> Nouveau dossier</a>
            <div class="nav-section-label">Analyse</div>
            <a href="#" class="nav-link"><i class="bi bi-graph-up-arrow"></i> Performance</a>
            <a href="#" class="nav-link"><i class="bi bi-pie-chart"></i> Répartition</a>
            <a href="#" class="nav-link"><i class="bi bi-file-earmark-text"></i> Rapports</a>
            <div class="nav-section-label">Compte</div>
            <a href="#" class="nav-link"><i class="bi bi-gear"></i> Paramètres</a>
            <a href="<?= base_url('backoffice/tarif') ?>" class="nav-link active"><i class="bi bi-coin"></i> Tarifs</a>
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
                    <div class="eyebrow mb-1">Paramètres</div>
                    <h1 class="h3 font-display mb-0">Gestion des tarifs</h1>
                    <p class="text-muted-soft mb-0">Modifiez les frais appliqués aux opérations.</p>
                </div>
            </div>

            <!-- Onglets -->
            <ul class="nav nav-tabs mb-4" id="tarifTabs" role="tablist">
                <?php foreach ($types as $type): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= ($type['id'] == $selected_type) ? 'active' : '' ?>"
                                data-type-id="<?= $type['id'] ?>"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-<?= $type['id'] ?>"
                                type="button" role="tab">
                            <?= ucfirst(strtolower($type['code'])) ?>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Contenu des onglets -->
            <div class="tab-content">
                <?php foreach ($types as $type): ?>
                    <div class="tab-pane fade <?= ($type['id'] == $selected_type) ? 'show active' : '' ?>"
                         id="tab-<?= $type['id'] ?>"
                         role="tabpanel">
                        <div class="card-chic">
                            <div class="card-header-chic">
                                <div>
                                    <div class="fw-semibold">Tarifs pour <?= ucfirst(strtolower($type['code'])) ?></div>
                                    <div class="text-faint small">Min / Max (Ar) — Prix (Ar)</div>
                                </div>
                            </div>
                            <div class="card-body-chic">
                                <div id="tarif-list-<?= $type['id'] ?>">
                                    <!--Ce bug reste là pour le moment--->
                                    <p class="text-muted-soft">Cliquer sur l'autre transfert puis revenir dans cette onglet </p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>

        <footer class="text-center text-faint small py-3">
            Mobile Money — Plateforme de gestion des transferts et retraits
        </footer>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Définir baseUrl pour les appels AJAX
    window.baseUrl = '<?= rtrim(base_url(), '/') ?>';
</script>
<script src="<?= base_url('assets/js/tarif.js') ?>"></script>
</body>
</html>