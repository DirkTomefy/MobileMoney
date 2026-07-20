<?= $this->extend('layouts/common_admin') ?>
<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-2">
    <div>
        <div class="eyebrow mb-1">Paramètres</div>
        <h1 class="h3 font-display mb-0">Gestion des tarifs</h1>
        <p class="text-muted-soft mb-0">Modifiez les frais appliqués aux opérations.</p>
    </div>
</div>

<ul class="nav nav-tabs mb-4" id="tarifTabs" role="tablist">
    <?php foreach ($types as $type): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= ($type['id'] == $selected_type) ? 'active' : '' ?>"
                    data-type-id="<?= $type['id'] ?>"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-<?= $type['id'] ?>"
                    type="button" role="tab">
                <?= ucfirst(strtolower($type['code'])) ?>
            </button>
        </li>
    <?php endforeach; ?>
</ul>

<div class="tab-content">
    <?php foreach ($types as $type): ?>
        <div class="tab-pane fade <?= ($type['id'] == $selected_type) ? 'show active' : '' ?>"
             id="tab-<?= $type['id'] ?>"
             role="tabpanel">
            <div class="card-chic">
                <div class="card-header-chic">
                    <div>
                        <div class="fw-semibold">Tarifs pour <?= ucfirst(strtolower($type['code'])) ?></div>
                        <div class="text-faint small">Min / Max (Ar) — Prix (Ar)</div>
                    </div>
                </div>
                <div class="card-body-chic">
                    <div id="tarif-list-<?= $type['id'] ?>">
                        <p class="text-muted-soft">Chargement...</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.baseUrl = '<?= rtrim(base_url(), '/') ?>';
</script>
<script src="<?= base_url('assets/js/tarif.js') ?>"></script>
<?= $this->endSection() ?>