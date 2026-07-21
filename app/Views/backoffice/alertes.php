<?= $this->extend('layouts/common_admin') ?>
<?= $this->section('content') ?>

<div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-2">
    <div>
        <div class="eyebrow mb-1">Surveillance</div>
        <h1 class="h3 font-display mb-0">Alertes transferts</h1>
        <p class="text-muted-soft mb-0">
            Détection des transferts dépassant le seuil de <?= number_format($seuil, 0, ',', ' ') ?> Ar
            du <?= date('d/m/Y', strtotime($date_min)) ?> au <?= date('d/m/Y', strtotime($date_max)) ?>.
        </p>
    </div>
    <div>
        <form method="get" class="d-flex gap-2 align-items-center">
            <div>
                <label for="date_min" class="form-label small">Du</label>
                <input type="date" name="date_min" id="date_min" class="form-control form-control-sm" value="<?= $date_min ?>">
            </div>
            <div>
                <label for="date_max" class="form-label small">Au</label>
                <input type="date" name="date_max" id="date_max" class="form-control form-control-sm" value="<?= $date_max ?>">
            </div>
            <div>
                <label for="seuil" class="form-label small">Seuil (Ar)</label>
                <input type="number" name="seuil" id="seuil" class="form-control form-control-sm" value="<?= $seuil ?>" step="50">
            </div>
            <button type="submit" class="btn btn-accent btn-sm mt-1"><i class="bi bi-filter"></i> Filtrer</button>
        </form>
    </div>
</div>

<!-- Statistiques -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card-chic stat-card">
            <div class="stat-value"><?= number_format(array_sum(array_column($statistiques, 'nb')), 0) ?></div>
            <div class="text-muted-soft small mt-1">Nombre d'alertes</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-chic stat-card">
            <div class="stat-value"><?= number_format(array_sum(array_column($statistiques, 'total_montant')), 0, ',', ' ') ?> Ar</div>
            <div class="text-muted-soft small mt-1">Montant total des alertes</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-chic stat-card">
            <div class="stat-value"><?= number_format(array_sum(array_column($statistiques, 'moyenne')) / max(1, count($statistiques)), 0, ',', ' ') ?> Ar</div>
            <div class="text-muted-soft small mt-1">Moyenne par jour</div>
        </div>
    </div>
</div>

<!-- Liste des alertes -->
<div class="card-chic">
    <div class="card-header-chic">
        <div>
            <div class="fw-semibold"><i class="bi bi-exclamation-triangle-fill me-2" style="color:#dc3545;"></i>Transactions suspectes</div>
            <div class="text-faint small">Transferts sortants dépassant le seuil défini</div>
        </div>
        <span class="badge-soft danger"><?= count($alertes) ?> alerte(s)</span>
    </div>
    <div class="table-responsive">
        <table class="table table-chic mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Source</th>
                    <th>Destinataire</th>
                    <th class="text-end">Montant (Ar)</th>
                    <th class="text-end">Frais (Ar)</th>
                    <th class="text-end">Commission (Ar)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($alertes)): ?>
                    <tr><td colspan="7" class="text-center text-muted-soft py-3">Aucune alerte pour cette période et ce seuil.</td></tr>
                <?php else: ?>
                    <?php foreach ($alertes as $a): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($a['date'])) ?></td>
                            <td><?= esc($a['source']) ?> (<?= esc($a['operateur_source']) ?>)</td>
                            <td><?= esc($a['cible'] ?? 'N/A') ?> (<?= esc($a['operateur_cible'] ?? 'N/A') ?>)</td>
                            <td class="text-end fw-bold"><?= number_format($a['montant'], 0, ',', ' ') ?></td>
                            <td class="text-end"><?= number_format($a['frais'], 0, ',', ' ') ?></td>
                            <td class="text-end"><?= number_format($a['commission'] ?? 0, 0, ',', ' ') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
