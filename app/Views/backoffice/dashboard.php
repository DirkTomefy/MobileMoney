<?php 
$session = \Config\Services::session();
$nom = $session->get('operateur_name');

?>

<?= $this->extend('layouts/common_admin') ?>
<?= $this->section('content') ?>

<div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-2">
    <div>
        <div class="eyebrow mb-1">Vue d'ensemble</div>
        <h1 class="h3 font-display mb-0">Bonjour, <?= $nom ?></h1>
        <p class="text-muted-soft mb-0">
            Synthèse des opérations du <?= date('d/m/Y', strtotime($date_min)) ?> au <?= date('d/m/Y', strtotime($date_max)) ?>.
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
            <button type="submit" class="btn btn-accent btn-sm mt-1"><i class="bi bi-filter"></i> Filtrer</button>
        </form>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-warning"><?= esc($error) ?></div>
<?php else: ?>

<!-- 5 KPI -->
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-2">
        <div class="card-chic stat-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="stat-icon"><i class="bi bi-cash-coin"></i></div>
                <span class="stat-delta up"><i class="bi bi-arrow-up-right"></i> +<?= number_format($total_transactions) ?></span>
            </div>
            <div class="stat-value"><?= number_format($total_frais, 0, ',', ' ') ?> Ar</div>
            <div class="text-muted-soft small mt-1">Frais totaux</div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="card-chic stat-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="stat-icon"><i class="bi bi-arrow-left-right"></i></div>
                <span class="stat-delta up"><i class="bi bi-arrow-up-right"></i> <?= number_format($total_transactions) ?></span>
            </div>
            <div class="stat-value"><?= number_format($montant_inter_operateur, 0, ',', ' ') ?> Ar</div>
            <div class="text-muted-soft small mt-1">Montant inter-opérateur</div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="card-chic stat-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="stat-icon"><i class="bi bi-arrow-up-circle"></i></div>
                <span class="stat-delta up"><i class="bi bi-arrow-up-right"></i> <?= $total_retrait['nb'] ?></span>
            </div>
            <div class="stat-value"><?= number_format($total_retrait['frais'], 0, ',', ' ') ?> Ar</div>
            <div class="text-muted-soft small mt-1">Frais de retrait</div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="card-chic stat-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="stat-icon"><i class="bi bi-arrow-right-circle"></i></div>
                <span class="stat-delta up"><i class="bi bi-arrow-up-right"></i> <?= $total_transfert['nb'] ?></span>
            </div>
            <div class="stat-value"><?= number_format($total_transfert['frais'], 0, ',', ' ') ?> Ar</div>
            <div class="text-muted-soft small mt-1">Frais de transfert</div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="card-chic stat-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="stat-icon"><i class="bi bi-arrow-left-right"></i></div>
                <span class="stat-delta up"><i class="bi bi-arrow-up-right"></i> <?= number_format($total_transactions) ?></span>
            </div>
            <div class="stat-value"><?= number_format($total_montant, 0, ',', ' ') ?> Ar</div>
            <div class="text-muted-soft small mt-1">Volume total</div>
        </div>
    </div>
</div>

<!-- Graphique -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card-chic h-100">
            <div class="card-header-chic">
                <div>
                    <div class="fw-semibold">Évolution des frais quotidiens</div>
                    <div class="text-faint small">Retraits et transferts</div>
                </div>
            </div>
            <div class="card-body-chic">
                <canvas id="chartFrais" height="150"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- TABLEAU RÉPARTITION INTER-OPÉRATEUR -->
<!-- TABLEAU RÉPARTITION INTER-OPÉRATEUR -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card-chic h-100">
            <div class="card-header-chic">
                <div>
                    <div class="fw-semibold">Répartition des transferts vers d'autres opérateurs</div>
                    <div class="text-faint small">Montants, frais et commissions par opérateur destinataire</div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-chic mb-0">
                    <thead>
                        <tr>
                            <th>Opérateur destinataire</th>
                            <th class="text-end">Nombre</th>
                            <th class="text-end">Montant total (Ar)</th>
                            <th class="text-end">Frais (Ar)</th>
                            <th class="text-end">Commission (Ar)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($repartition_inter_operateur)): ?>
                            <tr><td colspan="5" class="text-center text-muted-soft py-3">Aucun transfert inter-opérateur sur cette période.</td></tr>
                        <?php else: ?>
                            <?php foreach ($repartition_inter_operateur as $row): ?>
                                <tr>
                                    <td><?= esc($row['operateur_receveur'] ?? 'N/A') ?></td>
                                    <td class="text-end"><?= number_format($row['nb_transactions']) ?></td>
                                    <td class="text-end font-mono"><?= number_format($row['total_montant'], 0, ',', ' ') ?></td>
                                    <td class="text-end font-mono"><?= number_format($row['total_frais'], 0, ',', ' ') ?></td>
                                    <td class="text-end font-mono"><?= number_format($row['total_commission'] ?? 0, 0, ',', ' ') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Détail journalier -->
<div class="row g-3">
    <div class="col-12">
        <div class="card-chic h-100">
            <div class="card-header-chic">
                <div>
                    <div class="fw-semibold">Détail journalier</div>
                    <div class="text-faint small">Montants, frais et nombre d'opérations</div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-chic mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Retraits (nb / montant / frais)</th>
                            <th>Transferts (nb / montant / frais)</th>
                            <th>Total frais</th>
                            <th>Total transactions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($detail)): ?>
                            <tr><td colspan="5" class="text-center text-muted-soft py-3">Aucune donnée pour cette période.</td></tr>
                        <?php else: ?>
                            <?php foreach ($detail as $row): ?>
                                <tr>
                                    <td class="fw-semibold"><?= date('d/m/Y', strtotime($row['date'])) ?></td>
                                    <td>
                                        <span class="badge-soft info"><?= $row['retrait']['nb'] ?></span>
                                        <span class="font-mono"><?= number_format($row['retrait']['montant'], 0, ',', ' ') ?> Ar</span>
                                        <span class="text-faint">/ frais <?= number_format($row['retrait']['frais'], 0, ',', ' ') ?> Ar</span>
                                    </td>
                                    <td>
                                        <span class="badge-soft accent"><?= $row['transfert']['nb'] ?></span>
                                        <span class="font-mono"><?= number_format($row['transfert']['montant'], 0, ',', ' ') ?> Ar</span>
                                        <span class="text-faint">/ frais <?= number_format($row['transfert']['frais'], 0, ',', ' ') ?> Ar</span>
                                    </td>
                                    <td class="font-mono fw-semibold"><?= number_format($row['total_frais'], 0, ',', ' ') ?> Ar</td>
                                    <td><?= $row['total_transactions'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    window.chartData = {
        labels: <?= $labels ?: '[]' ?>,
        dataRetrait: <?= $data_retrait ?: '[]' ?>,
        dataTransfert: <?= $data_transfert ?: '[]' ?>
    };
</script>
<script src="<?= base_url('assets/js/dashboard.js') ?>"></script>
<?= $this->endSection() ?>