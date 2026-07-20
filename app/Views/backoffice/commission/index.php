<?= $this->extend('layouts/common_admin') ?>
<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-2">
    <div>
        <div class="eyebrow mb-1">Paramètres</div>
        <h1 class="h3 font-display mb-0">Gestion des commissions</h1>
        <p class="text-muted-soft mb-0">Définissez les pourcentages de commission entre opérateurs</p>
    </div>
    <a href="<?= base_url('backoffice/commission/create') ?>" class="btn btn-accent">
        <i class="bi bi-plus-lg"></i> Ajouter une commission
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible"><?= session()->getFlashdata('success') ?><button class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible"><?= session()->getFlashdata('error') ?><button class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="card-chic">
    <div class="card-header-chic">
        <div>
            <div class="fw-semibold">Liste des commissions</div>
            <div class="text-faint small"><?= count($commissions) ?> commission(s)</div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-chic mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Opérateur émetteur</th>
                    <th>Opérateur receveur</th>
                    <th>Pourcentage (%)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($commissions)): ?>
                    <tr><td colspan="5" class="text-center text-muted-soft py-3">Aucune commission définie.</td></tr>
                <?php else: ?>
                    <?php foreach ($commissions as $c): ?>
                        <tr>
                            <td><?= $c['id'] ?></td>
                            <td><?= esc($c['operateur_envoi']) ?></td>
                            <td><?= esc($c['operateur_receveur']) ?></td>
                            <td><span class="badge-soft accent"><?= $c['pourcentage'] ?>%</span></td>
                            <td>
                                <a href="<?= base_url('backoffice/commission/edit/' . $c['id']) ?>" class="btn btn-outline-accent btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?= base_url('backoffice/commission/delete/' . $c['id']) ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Supprimer cette commission ?')">
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
<?= $this->endSection() ?>