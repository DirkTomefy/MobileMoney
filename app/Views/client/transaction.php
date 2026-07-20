<?= $this->extend('layouts/common_client') ?>
<?= $this->section('styles') ?>
<style>
    :root {
        --color-accent: #8C6B3B;
        --color-bg-light: #f8f9fa;
        --color-border: #e9ecef;
        --color-text: #212529;
        --color-text-muted: #6c757d;
    }

    .transaction-container {
        max-width: 700px;
        margin: 0 auto;
    }

    .transaction-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }

    .transaction-types {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .type-button {
        padding: 1.25rem 1rem;
        border: 2px solid var(--color-border);
        border-radius: 10px;
        background: white;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
        font-weight: 500;
        color: var(--color-text-muted);
    }
    .type-button:hover {
        border-color: var(--color-accent);
        background: rgba(140, 107, 59, 0.05);
    }
    .type-button.active {
        border-color: var(--color-accent);
        background: var(--color-accent);
        color: white;
    }
    .type-button i {
        display: block;
        font-size: 1.75rem;
        margin-bottom: 0.5rem;
    }
    .type-button span {
        display: block;
        font-size: 0.95rem;
    }

    .form-section {
        display: none;
        animation: fadeIn 0.3s ease;
    }
    .form-section.active {
        display: block;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .fee-details {
        background: var(--color-bg-light);
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid var(--color-border);
    }
    .fee-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
    }
    .fee-row:last-child {
        margin-bottom: 0;
        padding-top: 0.75rem;
        border-top: 1px solid var(--color-border);
        font-weight: 600;
        font-size: 1rem;
    }
    .fee-label { color: var(--color-text-muted); }
    .fee-value { color: var(--color-text); font-family: 'IBM Plex Mono', monospace; }

    .solde-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding: 1rem;
        background: var(--color-bg-light);
        border-radius: 8px;
    }
    .solde-amount {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--color-accent);
        font-family: 'IBM Plex Mono', monospace;
    }

    .btn-accent {
        background: var(--color-accent);
        border: none;
        color: white;
        padding: 0.75rem 1rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .btn-accent:hover {
        background: #6d5328;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(140, 107, 59, 0.3);
    }
    .btn-secondary {
        background: var(--color-bg-light);
        border: 1px solid var(--color-border);
        color: var(--color-text);
        padding: 0.75rem 1rem;
        font-weight: 500;
    }
    .btn-secondary:hover {
        background: var(--color-border);
    }

    .button-group {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }
    .button-group .btn { flex: 1; }

    @media (max-width: 576px) {
        .transaction-card { padding: 1.5rem; }
        .transaction-types { grid-template-columns: 1fr; }
        .type-button { display: flex; align-items: center; gap: 1rem; padding: 1rem; }
        .type-button i { margin-bottom: 0; }
        .button-group { flex-direction: column; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="transaction-container">
    <div class="transaction-header">
        <h1 class="h3 font-display">Effectuer une Transaction</h1>
        <p class="text-muted-soft">Gérez vos mouvements financiers en toute sécurité</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <div class="transaction-card">
        <!-- Solde -->
        <div class="solde-section">
            <div>
                <div class="solde-label">Solde disponible</div>
                <div class="solde-amount"><?= number_format($solde ?? 0, 0, ',', ' ') ?> Ar</div>
            </div>
            <i class="bi bi-wallet2" style="font-size:2rem;color:var(--color-accent);opacity:0.3;"></i>
        </div>

        <!-- Types -->
        <div class="transaction-types">
            <button type="button" class="type-button active" data-type="retrait">
                <i class="bi bi-cash-coin"></i><span>Retrait</span>
            </button>
            <button type="button" class="type-button" data-type="depot">
                <i class="bi bi-plus-square"></i><span>Dépôt</span>
            </button>
            <button type="button" class="type-button" data-type="transfert">
                <i class="bi bi-arrow-left-right"></i><span>Transfert</span>
            </button>
        </div>
        <hr class="divider">

        <!-- RETRAIT -->
        <form class="form-section active" id="retraitForm" action="<?= base_url('client/retirer/save') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Montant à retirer</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-currency-exchange"></i></span>
                    <input type="number" class="form-control" name="montant" placeholder="Entrez le montant" min="100" required>
                    <span class="input-group-text">Ar</span>
                </div>
                <small class="text-muted">Montant minimum : 100 Ar</small>
            </div>
            <div class="button-group">
                <button type="submit" class="btn btn-accent"><i class="bi bi-check-circle me-2"></i>Confirmer le retrait</button>
                <button type="reset" class="btn btn-secondary"><i class="bi bi-arrow-counterclockwise me-2"></i>Annuler</button>
            </div>
        </form>

        <!-- DEPOT -->
        <form class="form-section" id="depotForm" action="<?= base_url('client/deposer/save') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Montant à déposer</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-currency-exchange"></i></span>
                    <input type="number" class="form-control" name="montant" placeholder="Entrez le montant" min="100" required>
                    <span class="input-group-text">Ar</span>
                </div>
                <small class="text-muted">Montant minimum : 100 Ar</small>
            </div>
            <div class="button-group">
                <button type="submit" class="btn btn-accent"><i class="bi bi-check-circle me-2"></i>Confirmer le dépôt</button>
                <button type="reset" class="btn btn-secondary"><i class="bi bi-arrow-counterclockwise me-2"></i>Annuler</button>
            </div>
        </form>

        <!-- TRANSFERT -->
        <form class="form-section" id="transfertForm" action="<?= base_url('client/transferer/save') ?>" method="post">
            <?= csrf_field() ?>
            <!-- NUMÉROS (multiples) -->
            <div class="mb-3">
                <label class="form-label">Numéro(s) du compte destinataire (séparés par une virgule)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-circle"></i></span>
                    <input type="text" class="form-control" name="numeros" id="numerosInput" placeholder="Ex: 0331234567, +261331234568" required>
                </div>
                <small class="text-muted">Séparez plusieurs numéros par une virgule. Le montant sera réparti équitablement.</small>
            </div>

            <!-- MONTANT TOTAL -->
            <div class="mb-3">
                <label class="form-label">Montant total à transférer</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-currency-exchange"></i></span>
                    <input type="number" class="form-control" name="montant_total" id="montantTotal" placeholder="Entrez le montant total" min="100" required>
                    <span class="input-group-text">Ar</span>
                </div>
                <small class="text-muted">Le montant sera divisé également entre les destinataires.</small>
            </div>

            <!-- DÉTAIL DES FRAIS -->
            <div class="fee-details">
                <div class="fee-row">
                    <span class="fee-label">Montant par destinataire :</span>
                    <span class="fee-value" id="feeAmountPerBenef">0 Ar</span>
                </div>
                <div class="fee-row">
                    <span class="fee-label">Frais de transfert (par bénéficiaire) :</span>
                    <span class="fee-value" id="feeFee">0 Ar</span>
                </div>
                <div class="fee-row" id="commissionRow" style="display:none;">
                    <span class="fee-label">Commission inter-opérateur :</span>
                    <span class="fee-value" id="feeCommission">0 Ar</span>
                </div>
                <div class="fee-row">
                    <span class="fee-label">Total à débiter par destinataire :</span>
                    <span class="fee-value" id="feeTotal">0 Ar</span>
                </div>
                <div class="fee-row" style="border-top:1px solid var(--color-border);padding-top:0.75rem;margin-top:0.75rem;">
                    <span class="fee-label">Total général à débiter :</span>
                    <span class="fee-value" id="feeGlobalTotal">0 Ar</span>
                </div>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="addFees" checked>
                <label class="form-check-label" for="addFees">Ajouter les frais et commissions au débit (sinon ils sont pris en charge par l'opérateur)</label>
            </div>

            <div class="button-group">
                <button type="submit" class="btn btn-accent"><i class="bi bi-check-circle me-2"></i>Confirmer le transfert</button>
                <button type="reset" class="btn btn-secondary"><i class="bi bi-arrow-counterclockwise me-2"></i>Annuler</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Variables initiales
    const operateurSource = <?= json_encode($client['id_operateur'] ?? 0) ?>;
    let operateurCible = null;
    let autreOperateur = false;
    let pourcentageCommission = 0;
    let fraisParTransfert = 0;

    // Éléments
    const numerosInput = document.getElementById('numerosInput');
    const montantTotalInput = document.getElementById('montantTotal');
    const addFeesCheckbox = document.getElementById('addFees');

    const feeAmountPerBenef = document.getElementById('feeAmountPerBenef');
    const feeFeeSpan = document.getElementById('feeFee');
    const feeCommissionSpan = document.getElementById('feeCommission');
    const feeTotalSpan = document.getElementById('feeTotal');
    const feeGlobalTotal = document.getElementById('feeGlobalTotal');
    const commissionRow = document.getElementById('commissionRow');

    // Fonction pour récupérer les infos d'un numéro
    async function fetchInfoNumero(numero) {
        try {
            const resp = await fetch("<?= base_url('client/info-numero?numero=') ?>" + encodeURIComponent(numero));
            return await resp.json();
        } catch {
            return { success: false };
        }
    }

    // Fonction pour récupérer la commission
    async function fetchCommission(envoi, receveur) {
        try {
            const resp = await fetch("<?= base_url('client/get-commission?id_operateur_envoi=') ?>" + envoi + "&id_operateur_receveur=" + receveur);
            return await resp.json();
        } catch {
            return { success: false, pourcentage: 0 };
        }
    }

    // Fonction pour obtenir les frais de transfert pour un montant (appel API)
    async function fetchFraisTransfert(montant) {
        // Simule un appel pour récupérer les frais (vous pouvez créer une API dédiée)
        // Ici on utilise un calcul fictif basé sur des paliers (à remplacer par votre logique)
        // Pour l'exemple, on simule via un appel AJAX à un endpoint existant ?
        // On va faire un fetch GET vers un contrôleur qui renvoie les frais
        try {
            const resp = await fetch("<?= base_url('client/get-frais-transfert?montant=') ?>" + montant);
            return await resp.json();
        } catch {
            return { frais: 0 };
        }
    }

    // Fonction principale de mise à jour
    async function updateFees() {
        const numeros = numerosInput.value.split(',').map(s => s.trim()).filter(s => s !== '');
        const nbBenef = numeros.length;
        const montantTotal = parseFloat(montantTotalInput.value) || 0;
        const montantParBenef = nbBenef > 0 ? montantTotal / nbBenef : 0;

        // Réinitialiser
        let fraisUnitaire = 0;
        let commissionUnitaire = 0;
        let operateurCible = null;
        let autreOperateur = false;

        if (nbBenef > 0 && montantParBenef > 0) {
            // Pour le premier bénéficiaire, on vérifie l'opérateur (on suppose que tous les bénéficiaires sont du même opérateur ? 
            // Dans la réalité, ils pourraient être différents. On va simplifier : on prend le premier bénéficiaire pour déterminer la commission.
            // On pourrait aussi faire une moyenne, mais c'est complexe. On va utiliser le premier.
            const firstNumero = numeros[0];
            const info = await fetchInfoNumero(firstNumero);
            if (info.success) {
                const idCible = info.id_operateur;
                if (idCible && idCible !== operateurSource) {
                    autreOperateur = true;
                    const commData = await fetchCommission(operateurSource, idCible);
                    pourcentageCommission = commData.success ? commData.pourcentage : 0;
                    commissionUnitaire = montantParBenef * pourcentageCommission / 100;
                } else {
                    autreOperateur = false;
                    pourcentageCommission = 0;
                    commissionUnitaire = 0;
                }
            }

            // Récupérer les frais de transfert pour ce montant unitaire
            const fraisData = await fetchFraisTransfert(montantParBenef);
            fraisUnitaire = fraisData.frais || 0;
        }

        // Affichage
        feeAmountPerBenef.textContent = montantParBenef.toLocaleString('fr-FR') + ' Ar';
        feeFeeSpan.textContent = fraisUnitaire.toLocaleString('fr-FR') + ' Ar';

        if (autreOperateur && commissionUnitaire > 0) {
            commissionRow.style.display = 'flex';
            feeCommissionSpan.textContent = commissionUnitaire.toLocaleString('fr-FR') + ' Ar';
        } else {
            commissionRow.style.display = 'none';
            feeCommissionSpan.textContent = '0 Ar';
        }

        const addFees = addFeesCheckbox.checked;
        const totalParBenef = addFees ? montantParBenef + fraisUnitaire + commissionUnitaire : montantParBenef;
        feeTotalSpan.textContent = totalParBenef.toLocaleString('fr-FR') + ' Ar';
        feeGlobalTotal.textContent = (totalParBenef * nbBenef).toLocaleString('fr-FR') + ' Ar';
    }

    // Écouteurs
    numerosInput.addEventListener('input', updateFees);
    montantTotalInput.addEventListener('input', updateFees);
    addFeesCheckbox.addEventListener('change', updateFees);

    // Initialisation
    updateFees();

    // Gestion des onglets (inchangée)
    const typeButtons = document.querySelectorAll('.type-button');
    const formSections = document.querySelectorAll('.form-section');
    typeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.getAttribute('data-type');
            typeButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            formSections.forEach(f => f.classList.remove('active'));
            document.getElementById(type + 'Form').classList.add('active');
        });
    });
</script>
<?= $this->endSection() ?>