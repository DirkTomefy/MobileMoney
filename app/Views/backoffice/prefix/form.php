<?= $this->extend('layouts/common_admin') ?>
<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-2">
    <div>
        <div class="eyebrow mb-1">Paramètres</div>
        <h1 class="h3 font-display mb-0"><?= isset($prefix) ? 'Modifier' : 'Ajouter' ?> un préfixe</h1>
        <p class="text-muted-soft mb-0">Exemple : 033 ou +26133</p>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card-chic" style="max-width:600px;">
    <div class="card-body-chic">
        <form action="<?= isset($prefix) ? base_url('backoffice/prefix/update/' . $prefix['id']) : base_url('backoffice/prefix/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="id_operateur" class="form-label">Opérateur</label>
                <select class="form-select" id="id_operateur" name="id_operateur" required>
                    <option value="">Choisir...</option>
                    <?php foreach ($operateurs as $op): ?>
                        <option value="<?= $op['id'] ?>" <?= (isset($prefix) && $prefix['id_operateur'] == $op['id']) ? 'selected' : '' ?>>
                            <?= esc($op['libelle']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="libelle" class="form-label">Libellé du préfixe</label>
                <input type="text" class="form-control" id="libelle" name="libelle" placeholder="Ex: 033" value="<?= isset($prefix) ? esc($prefix['libelle']) : '' ?>" required>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-accent"><?= isset($prefix) ? 'Mettre à jour' : 'Enregistrer' ?></button>
                <a href="<?= base_url('backoffice/prefix') ?>" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>