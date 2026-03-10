<?php
// view/Dashboard.php
if (!isset($conn)) {
    echo "<div class='alert alert-danger'>Error: Conexión de base de datos no disponible.</div>";
    return;
}

$inspector = new DatabaseInspector($conn);
$tablas = $inspector->getTables();
?>

<div class="row">
    <?php if (empty($tablas)): ?>
        <div class="col-12 text-center p-5">
            <i class="fas fa-database fa-4x mb-3 text-muted" style="opacity: 0.2"></i>
            <h4 class="text-white">No se detectaron tablas en la base de datos</h4>
        </div>
    <?php endif; ?>

    <?php foreach ($tablas as $t): 
        $tableName = strtolower($t['name']);
    ?>
    <div class="col-lg-3 col-6 mb-4">
        <div class="small-box bg-surface border-glass shadow-glow h-100 d-flex flex-column justify-content-between">
            <div class="inner p-4">
                <h3 class="text-brand font-weight-bold" style="font-size: 2.2rem;"><?= $t['count'] ?></h3>
                <p class="text-white-custom mb-0" style="letter-spacing: 1px;">
                    Registros en <span class="text-accent"><?= ucfirst($t['name']) ?></span>
                </p>
            </div>
            <div class="icon">
                <i class="fas fa-table text-accent" style="opacity: 0.1; font-size: 5rem; top: 10px; right: 15px;"></i>
            </div>
            <a href="<?= BASE_URL . $tableName ?>" class="small-box-footer bg-glass-dark-custom py-2" style="border-radius: 0 0 12px 12px; border-top: 1px solid rgba(255,255,255,0.05);">
                Gestionar Módulo <i class="fas fa-arrow-circle-right ml-2"></i>
            </a>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<style>
    .small-box { transition: transform 0.3s ease, box-shadow 0.3s ease; border-radius: 12px !important; }
    .small-box:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(168, 85, 247, 0.2) !important; }
    .bg-surface { background: rgba(255, 255, 255, 0.03) !important; }
    .text-brand { color: var(--brand-primary); }
</style>