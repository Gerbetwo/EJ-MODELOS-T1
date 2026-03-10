<?php
$modulos = TableRegistry::getAllModules();
$urlParams = explode('/', trim($_GET['url'] ?? 'dashboard', '/'));
$activeModule = $urlParams[0];
?>
<aside class="main-sidebar sidebar-dark-primary bg-transparent elevation-4 sidebar-mini sidebar-no-expand">
    <a href="<?= BASE_URL ?>" class="brand-link border-glass">
        <span class="brand-text font-weight-light text-accent-custom ml-3">Gestión Udenar</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>" class="nav-link <?= ($activeModule == 'dashboard') ? 'active shadow-primary' : '' ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header text-muted small">BASES DE DATOS</li>

                <?php foreach ($modulos as $m): ?>
                    <li class="nav-item">
                        <a href="<?= BASE_URL . $m ?>" 
                           class="nav-link <?= ($activeModule === $m) ? 'active shadow-primary' : '' ?>">
                            <i class="nav-icon fas fa-database"></i>
                            <p><?= ucfirst($m) ?></p>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>
</aside>