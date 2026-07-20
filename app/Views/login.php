<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion — Mobile Money</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
  <style>
    .tab-buttons {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.5rem;
      border-bottom: 2px solid #e9ecef;
    }

    .tab-button {
      background: none;
      border: none;
      padding: 0.75rem 1rem;
      font-size: 1rem;
      font-weight: 500;
      color: #6c757d;
      cursor: pointer;
      position: relative;
      transition: color 0.3s ease;
    }

    .tab-button.active {
      color: var(--color-accent, #A9814B);
    }

    .tab-button.active::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      right: 0;
      height: 2px;
      background: var(--color-accent, #A9814B);
    }

    .tab-button:hover {
      color: var(--color-accent, #A9814B);
    }

    .form-section {
      display: none;
    }

    .form-section.active {
      display: block;
    }
  </style>
</head>

<body>

  <div class="auth-shell">

    <!-- Visual side -->
    <div class="auth-visual">
      <div class="d-flex align-items-center gap-3">
        <div class="brand-mark">MM</div>
        <div class="brand-word">Mobile Money<small>Plateforme de transferts</small></div>
      </div>

      <div class="quote">
        « La rigueur est une élégance. Chaque décision mérite la clarté des chiffres qui la précèdent. »
      </div>

      <div>
        <hr class="rule-brass" style="background:var(--color-accent); opacity:.9;">
        <div class="d-flex gap-4">
          <div>
            <div class="font-display fs-4">Ar 2.4Md</div>
            <div class="eyebrow" style="color:#8C6B3B;">de transactions</div>
          </div>
          <div>
            <div class="font-display fs-4">99.98%</div>
            <div class="eyebrow" style="color:#8C6B3B;">disponibilité</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Form side -->
    <div class="auth-form-col">
      <div class="auth-card">

        <div class="mb-4 d-lg-none d-flex align-items-center gap-2">
          <div class="brand-mark dark">MM</div>
          <div class="brand-word dark">Mobile Money</div>
        </div>

        <div class="eyebrow mb-2">Espace privé</div>
        <h1 class="h3 font-display mb-1">Bon retour parmi nous</h1>
        <p class="text-muted-soft mb-4">Connectez-vous pour accéder à votre tableau de bord.</p>

        <?php if (session()->getFlashdata('error')): ?>
          <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
          </div>
        <?php endif; ?>

        <!-- Tab Buttons -->
        <div class="tab-buttons">
          <button type="button" class="tab-button active" data-tab="client">
            <i class="bi bi-person me-2"></i>Client
          </button>
          <button type="button" class="tab-button" data-tab="operateur">
            <i class="bi bi-briefcase me-2"></i>Opérateur
          </button>
        </div>

        <!-- CLIENT FORM -->
        <form class="form-section active" id="clientForm" novalidate action="<?= base_url('home/connect') ?>" method="post">
          <div class="mb-3">
            <label for="phone" class="form-label">Numéro de téléphone</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-telephone"></i></span>
              <input type="tel" class="form-control" id="phone" placeholder="+261 XX XXX XXX" required name="phone">
            </div>
          </div>

          <button type="submit" class="btn btn-accent w-100 py-2">
            Se connecter <i class="bi bi-arrow-right ms-1"></i>
          </button>
        </form>

        <!-- OPERATEUR FORM -->
        <form class="form-section" id="operateurForm" novalidate action="<?= base_url('home/connectOperateur') ?>" method="post">
          <div class="mb-3">
            <label for="operateur_id" class="form-label">Sélectionnez votre opérateur</label>
            <select class="form-select" id="operateur_id" name="operateur_id" required>
              <option value="">Choisir...</option>
              <?php foreach ($operateurs as $op): ?>
                <option value="<?= $op['id'] ?>"><?= esc($op['libelle']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <button type="submit" class="btn btn-accent w-100 py-2">
            Se connecter <i class="bi bi-arrow-right ms-1"></i>
          </button>
        </form>

        <div class="divider-text" id="divider" style="display: none;">ou continuer avec</div>

        <div class="d-flex gap-2" id="socialButtons" style="display: none;">
          <button class="btn btn-social flex-fill rounded-3"><i class="bi bi-phone"></i> Mobile Money</button>
        </div>

        <p class="text-center text-muted-soft small mt-4 mb-0">
          Pas encore de compte ? <a href="#">Demander un accès</a>
        </p>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Tab switching
    const tabButtons = document.querySelectorAll('.tab-button');
    const formSections = document.querySelectorAll('.form-section');
    const divider = document.getElementById('divider');
    const socialButtons = document.getElementById('socialButtons');

    tabButtons.forEach(button => {
      button.addEventListener('click', function() {
        const tabName = this.getAttribute('data-tab');

        // Remove active class from all buttons and forms
        tabButtons.forEach(btn => btn.classList.remove('active'));
        formSections.forEach(form => form.classList.remove('active'));

        // Add active class to clicked button and corresponding form
        this.classList.add('active');
        document.getElementById(tabName + 'Form').classList.add('active');

        // Show/hide social buttons based on tab
        if (tabName === 'client') {
          divider.style.display = 'none';
          socialButtons.style.display = 'none';
        } else {
          divider.style.display = 'block';
          socialButtons.style.display = 'flex';
        }
      });
    });

    // Form submission client
    document.getElementById('clientForm').addEventListener('submit', function(e) {
      const phone = document.getElementById('phone').value.trim();
      if (!phone) {
        e.preventDefault();
        alert("Veuillez entrer votre numéro.");
      }
    });

    // Form submission operateur : validation du select
    document.getElementById('operateurForm').addEventListener('submit', function(e) {
      const operateurId = document.getElementById('operateur_id').value;
      if (!operateurId) {
        e.preventDefault();
        alert("Veuillez sélectionner un opérateur.");
      }
    });
  </script>
</body>

</html>