<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des préfixes — Mobile Money</title>
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
            <div class="brand-word">Mobile Money<small>Backoffice</small></div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section-label">Général</div>
            <a href="<?= base_url('backoffice/dashboard') ?>" class="nav-link"><i class="bi bi-grid-1x2"></i> Tableau de bord</a>
            <a href="<?= base_url('backoffice/portefeuille') ?>" class="nav-link"><i class="bi bi-wallet2"></i> Portefeuille</a>
            <a href="#" class="nav-link"><i class="bi bi-table"></i> Clients</a>
            <div class="nav-section-label">Paramètres</div>
            <a href="<?= base_url('backoffice/tarif') ?>" class="nav-link"><i class="bi bi-coin"></i> Tarifs</a>
            <a href="<?= base_url('backoffice/prefix') ?>" class="nav-link active"><i class="bi bi-tags"></i> Préfixes</a>
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
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-2">
                <div>
                    <div class="eyebrow mb-1">Paramètres</div>
                    <h1 class="h3 font-display mb-0">Gestion des préfixes</h1>
                    <p class="text-muted-soft mb-0">Gérez les préfixes des numéros de téléphone</p>
                </div>
                <a href="<?= base_url('backoffice/prefix/create') ?>" class="btn btn-accent">
                    <i class="bi bi-plus-lg"></i> Ajouter un préfixe
                </a>
            </div>

            <div class="card-chic">
                <div class="card-header-chic">
                    <div>
                        <div class="fw-semibold">Liste des préfixes</div>
                        <div class="text-faint small"><?= count($prefixes) ?> préfixe(s)</div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-chic mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Libellé</th>
                                <th>Opérateur</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($prefixes)): ?>
                                <tr><td colspan="4" class="text-center text-muted-soft py-3">Aucun préfixe.</td></tr>
                            <?php else: ?>
                                <?php foreach ($prefixes as $p): ?>
                                    <tr>
                                        <td><?= $p['id'] ?></td>
                                        <td><span class="font-mono"><?= esc($p['libelle']) ?></span></td>
                                        <td>
                                            <?php
                                                $op = array_filter($operateurs, fn($o) => $o['id'] == $p['id_operateur']);
                                                echo esc(reset($op)['libelle'] ?? 'Inconnu');
                                            ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('backoffice/prefix/edit/' . $p['id']) ?>" class="btn btn-outline-accent btn-sm">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="<?= base_url('backoffice/prefix/delete/' . $p['id']) ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Supprimer ce préfixe ?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
        <footer class="text-center text-faint small py-3">Mobile Money — Backoffice</footer>
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