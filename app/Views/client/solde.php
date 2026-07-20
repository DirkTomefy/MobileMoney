<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon solde — Mobile Money</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>
<div class="app-shell">

    <!-- SIDEBAR client -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-mark">MM</div>
            <div class="brand-word">Mobile Money<small>Mon espace</small></div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section-label">Général</div>
            <a href="<?= base_url('client/solde') ?>" class="nav-link active"><i class="bi bi-wallet2"></i> Mon solde</a>
            <a href="#" class="nav-link"><i class="bi bi-clock-history"></i> Historique</a>
            <a href="#" class="nav-link"><i class="bi bi-person"></i> Profil</a>
            <div class="nav-section-label">Actions</div>
            <a href="<?= base_url('client/solde/action/depot') ?>" class="nav-link"><i class="bi bi-plus-circle"></i> Dépôt</a>
            <a href="<?= base_url('client/solde/action/retrait') ?>" class="nav-link"><i class="bi bi-dash-circle"></i> Retrait</a>
            <a href="<?= base_url('client/solde/action/transfert') ?>" class="nav-link"><i class="bi bi-arrow-left-right"></i> Transfert</a>
            <div class="nav-section-label">Compte</div>
            <a href="#" class="nav-link"><i class="bi bi-gear"></i> Paramètres</a>
            <a href="<?= base_url('logout') ?>" class="nav-link"><i class="bi bi-box-arrow-right"></i> Déconnexion</a>
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
                    <div class="avatar-sm"><?= strtoupper(substr(session()->get('numero'), -2)) ?></div>
                    <div class="d-none d-sm-block">
                        <div class="fw-semibold small">Client</div>
                        <div class="text-faint" style="font-size:.72rem;"><?= session()->get('numero') ?></div>
                    </div>
                    <i class="bi bi-chevron-down text-faint small"></i>
                </div>
            </div>
        </header>

        <main class="main-content">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-2">
                <div>
                    <div class="eyebrow mb-1">Portefeuille</div>
                    <h1 class="h3 font-display mb-0">Votre solde</h1>
                    <p class="text-muted-soft mb-0">Au <?= date('d/m/Y', strtotime($date_solde)) ?></p>
                </div>
                <div class="d-flex gap-2">
                    <form method="get" class="d-flex gap-2 align-items-center">
                        <div>
                            <label for="date" class="form-label small">Date</label>
                            <input type="date" name="date" id="date" class="form-control form-control-sm" value="<?= $date_solde ?>">
                        </div>
                        <button type="submit" class="btn btn-accent btn-sm mt-1"><i class="bi bi-refresh"></i></button>
                    </form>
                </div>
            </div>

            <!-- Solde -->
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <div class="card-chic stat-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted-soft small">Solde disponible</div>
                                <div class="stat-value" style="font-size:2.5rem;"><?= number_format($solde, 0, ',', ' ') ?> Ar</div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="<?= base_url('client/solde/action/depot') ?>" class="btn btn-accent"><i class="bi bi-plus-lg"></i> Dépôt</a>
                                <a href="<?= base_url('client/solde/action/retrait') ?>" class="btn btn-outline-accent"><i class="bi bi-dash-lg"></i> Retrait</a>
                                <a href="<?= base_url('client/solde/action/transfert') ?>" class="btn btn-outline-accent"><i class="bi bi-arrow-right"></i> Transfert</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtres -->
            <div class="row g-3 mb-3">
                <div class="col-md-12">
                    <form method="get" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small">Type</label>
                            <select name="type" class="form-select form-select-sm">
                                <option value="">Tous</option>
                                <?php foreach ($types as $type): ?>
                                    <option value="<?= $type['id'] ?>" <?= ($filtre_type == $type['id']) ? 'selected' : '' ?>>
                                        <?= ucfirst(strtolower($type['code'])) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Date min</label>
                            <input type="date" name="date_min" class="form-control form-control-sm" value="<?= $filtre_date_min ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Date max</label>
                            <input type="date" name="date_max" class="form-control form-control-sm" value="<?= $filtre_date_max ?>">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-accent btn-sm w-100"><i class="bi bi-filter"></i> Filtrer</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste -->
            <div class="card-chic">
                <div class="card-header-chic">
                    <div>
                        <div class="fw-semibold">Historique des transactions</div>
                        <div class="text-faint small"><?= $total ?> opération(s)</div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-chic mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Source</th>
                                <th>Cible</th>
                                <th>Montant</th>
                                <th>Frais</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($transactions)): ?>
                                <tr><td colspan="6" class="text-center text-muted-soft py-3">Aucune transaction.</td></tr>
                            <?php else: ?>
                                <?php foreach ($transactions as $tr): ?>
                                    <tr>
                                        <td class="font-mono small"><?= date('d/m/Y H:i', strtotime($tr['date'])) ?></td>
                                        <td><span class="badge-soft <?= ($tr['type_code'] == 'DEPOT') ? 'success' : (($tr['type_code'] == 'RETRAIT') ? 'danger' : 'info') ?>">
                                            <?= ucfirst(strtolower($tr['type_code'])) ?>
                                        </span></td>
                                        <td><?= $tr['source_prenom'] ?? '' ?> <?= $tr['source_nom'] ?? '' ?> <span class="text-faint small">(<?= $tr['source_numero'] ?>)</span></td>
                                        <td><?= $tr['cible_prenom'] ?? '' ?> <?= $tr['cible_nom'] ?? '' ?> <span class="text-faint small">(<?= $tr['cible_numero'] ?>)</span></td>
                                        <td class="font-mono"><?= number_format($tr['montant'], 0, ',', ' ') ?> Ar</td>
                                        <td class="font-mono"><?= number_format($tr['frais'], 0, ',', ' ') ?> Ar</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($pager): ?>
                    <div class="card-footer-chic d-flex justify-content-center">
                        <?= $pager->links() ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>

        <footer class="text-center text-faint small py-3">
            Mobile Money — Plateforme de gestion des transferts et retraits
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