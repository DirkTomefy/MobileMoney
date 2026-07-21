<?= $this->extend('layouts/common_client') ?>
<?= $this->section('content') ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>
<div class="">

    <form action="<?= base_url('client/eparge/ajouter') ?>" method="post">
        <input type="number" name="pourcentage" id="pourcentage">
        <input type="submit" value="accepter">
    </form>

</div>

<?= $this->endSection() ?>