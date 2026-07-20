<?= $this->extend('layouts/common_admin') ?>
<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-2">
    <div>
        <div class="eyebrow mb-1">Gestion des portefeuilles</div>
        <h1 class="h3 font-display mb-0">État des comptes clients</h1>
        <p class="text-muted-soft mb-0">Solde des clients à la date sélectionnée</p>
    </div>
    <div class="d-flex gap-2">
        <form method="get" class="d-flex gap-2 align-items-center">
            <div>
                <label for="date" class="form-label small">Date</label>
                <input type="date" name="date" id="date" class="form-control form-control-sm" value="<?= $date ?>">
            </div>
            <button type="submit" class="btn btn-accent btn-sm mt-1"><i class="bi bi-refresh"></i> Mettre à jour</button>
        </form>
    </div>
</div>

<div class="card-chic">
    <div class="card-header-chic">
        <div>
            <div class="fw-semibold">Liste des clients</div>
            <div class="text-faint small"><?= count($portefeuille) ?> client(s) actif(s)</div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-chic mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Numéro</th>
                    <th>Opérateur</th>
                    <th>Date création</th>
                    <th>Solde (Ar)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($portefeuille)): ?>
                    <tr><td colspan="7" class="text-center text-muted-soft py-3">Aucun client trouvé.</td></tr>
                <?php else: ?>
                    <?php foreach ($portefeuille as $client): ?>
                        <tr>
                            <td><?= $client['id'] ?></td>
                            <td><?= esc($client['nom']) ?></td>
                            <td><?= esc($client['prenom']) ?></td>
                            <td><?= esc($client['numero']) ?></td>
                            <td><?= esc($client['operateur']) ?></td>
                            <td><?= date('d/m/Y', strtotime($client['date_creation'])) ?></td>
                            <td class="font-mono fw-semibold <?= ($client['solde'] < 0) ? 'text-danger' : '' ?>">
                                <?= number_format($client['solde'], 0, ',', ' ') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>