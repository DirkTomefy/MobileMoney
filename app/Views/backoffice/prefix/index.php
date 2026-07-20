<?= $this->extend('layouts/common_admin') ?>
<?= $this->section('content') ?>
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

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible"><?= session()->getFlashdata('success') ?><button class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible"><?= session()->getFlashdata('error') ?><button class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

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
<?= $this->endSection() ?>