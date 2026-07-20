<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Mobile Money — Mon espace' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
    <?= $this->renderSection('styles') ?>
</head>
<body>
<div class="app-shell">
    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-mark">MM</div>
            <div class="brand-word">Mobile Money<small>Mon espace</small></div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section-label">Général</div>
            <a href="<?= base_url('client/solde') ?>" class="nav-link <?= (current_url() == base_url('client/solde')) ? 'active' : '' ?>">
                <i class="bi bi-wallet2"></i> Mon solde
            </a>
            <a href="#" class="nav-link"><i class="bi bi-clock-history"></i> Historique</a>
            <a href="#" class="nav-link"><i class="bi bi-person"></i> Profil</a>
            <div class="nav-section-label">Actions</div>
            <a href="<?= base_url('client/solde/action/depot') ?>" class="nav-link"><i class="bi bi-plus-circle"></i> Dépôt</a>
            <a href="<?= base_url('client/solde/action/retrait') ?>" class="nav-link"><i class="bi bi-dash-circle"></i> Retrait</a>
            <a href="<?= base_url('client/solde/action/transfert') ?>" class="nav-link"><i class="bi bi-arrow-left-right"></i> Transfert</a>
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
            </div>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-accent position-relative">
                    <i class="bi bi-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background:var(--color-accent); width:8px; height:8px; padding:0;"></span>
                </button>
                <div class="vr d-none d-sm-block"></div>
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar-sm"><?= strtoupper(substr(session()->get('numero') ?? 'CL', -2)) ?></div>
                    <div class="d-none d-sm-block">
                        <div class="fw-semibold small">Client</div>
                        <div class="text-faint" style="font-size:.72rem;"><?= session()->get('numero') ?? '' ?></div>
                    </div>
                    <i class="bi bi-chevron-down text-faint small"></i>
                </div>
            </div>
        </header>

        <main class="main-content">
            <?= $this->renderSection('content') ?>
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
<?= $this->renderSection('scripts') ?>
</body>
</html>