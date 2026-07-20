<?= $this->extend('layouts/common_admin') ?>
<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-2">
    <div>
        <div class="eyebrow mb-1">Paramètres</div>
        <h1 class="h3 font-display mb-0"><?= isset($commission) ? 'Modifier' : 'Ajouter' ?> une commission</h1>
        <p class="text-muted-soft mb-0">Définissez le pourcentage appliqué entre deux opérateurs</p>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card-chic" style="max-width:600px;">
    <div class="card-body-chic">
        <form action="<?= isset($commission) ? base_url('backoffice/commission/update/' . $commission['id']) : base_url('backoffice/commission/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="id_operateur_envoi" class="form-label">Opérateur émetteur</label>
                <select class="form-select" id="id_operateur_envoi" name="id_operateur_envoi" required>
                    <option value="">Choisir...</option>
                    <?php foreach ($operateurs as $op): ?>
                        <option value="<?= $op['id'] ?>" <?= (isset($commission) && $commission['id_operateur_envoi'] == $op['id']) ? 'selected' : '' ?>>
                            <?= esc($op['libelle']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="id_operateur_receveur" class="form-label">Opérateur receveur</label>
                <select class="form-select" id="id_operateur_receveur" name="id_operateur_receveur" required>
                    <option value="">Choisir...</option>
                    <?php foreach ($operateurs as $op): ?>
                        <option value="<?= $op['id'] ?>" <?= (isset($commission) && $commission['id_operateur_receveur'] == $op['id']) ? 'selected' : '' ?>>
                            <?= esc($op['libelle']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="pourcentage" class="form-label">Pourcentage (%)</label>
                <input type="number" class="form-control" id="pourcentage" name="pourcentage" placeholder="Ex: 30" step="0.01" min="0.01" max="100" value="<?= isset($commission) ? esc($commission['pourcentage']) : '' ?>" required>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-accent"><?= isset($commission) ? 'Mettre à jour' : 'Enregistrer' ?></button>
                <a href="<?= base_url('backoffice/commission') ?>" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>