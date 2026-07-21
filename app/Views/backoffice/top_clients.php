<?= $this->extend('layouts/common_admin') ?>
<?= $this->section('content') ?>

<div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-2">
    <div>
        <div class="eyebrow mb-1">Analyse</div>
        <h1 class="h3 font-display mb-0">Top clients</h1>
        <p class="text-muted-soft mb-0">
            Classement des clients par montant total ou nombre de transactions
            du <?= date('d/m/Y', strtotime($date_min)) ?> au <?= date('d/m/Y', strtotime($date_max)) ?>.
        </p>
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
            <div>
                <label for="order" class="form-label small">Trier par</label>
                <select name="order" id="order" class="form-select form-select-sm">
                    <option value="montant" <?= $orderBy === 'montant' ? 'selected' : '' ?>>Montant total</option>
                    <option value="nb" <?= $orderBy === 'nb' ? 'selected' : '' ?>>Nombre de transactions</option>
                </select>
            </div>
            <button type="submit" class="btn btn-accent btn-sm mt-1"><i class="bi bi-filter"></i> Filtrer</button>
        </form>
    </div>
</div>

<div class="card-chic">
    <div class="card-body-chic">
        <div class="table-responsive">
            <table class="table table-chic mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Numéro</th>
                        <th class="text-end">Nombre de transactions</th>
                        <th class="text-end">Montant total (Ar)</th>
                        <th class="text-end">Frais totaux (Ar)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($topClients)): ?>
                        <tr><td colspan="6" class="text-center text-muted-soft py-3">Aucun client sur cette période.</td></tr>
                    <?php else: ?>
                        <?php $rank = 1; ?>
                        <?php foreach ($topClients as $client): ?>
                            <tr>
                                <td><?= $rank++ ?></td>
                                <td><?= esc($client['nom'] . ' ' . $client['prenom']) ?></td>
                                <td><?= esc($client['numero']) ?></td>
                                <td class="text-end"><?= number_format($client['nb_transactions']) ?></td>
                                <td class="text-end font-mono"><?= number_format($client['total_montant'], 0, ',', ' ') ?></td>
                                <td class="text-end font-mono"><?= number_format($client['total_frais'], 0, ',', ' ') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>