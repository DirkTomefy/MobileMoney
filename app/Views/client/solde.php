<?= $this->extend('layouts/common_client') ?>
<?= $this->section('content') ?>
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
<?= $this->endSection() ?>