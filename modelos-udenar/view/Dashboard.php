<?php
// view/dashboard.php
$inspector = new DatabaseInspector($conn);
$tablas = $inspector->getTables();
?>
<div class="row">
    <?php foreach ($tablas as $t): ?>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-surface border-glass shadow-glow">
                <div class="inner">
                    <h3 class="text-brand"><?= $t['count'] ?></h3>
                    <p class="text-white-custom">Registros en <b><?= ucfirst($t['name']) ?></b></p>
                </div>
                <div class="icon">
                    <i class="fas fa-table text-accent" style="opacity: 0.3"></i>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>