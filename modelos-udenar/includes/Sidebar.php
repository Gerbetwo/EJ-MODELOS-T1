<?php
$modulos = TableRegistry::getAllModules();
$currentUrl = $_GET['url'] ?? 'dashboard';
?>
<aside class="main-sidebar sidebar-dark-primary bg-transparent elevation-4 sidebar-mini">
    <a href="<?= BASE_URL ?>" class="brand-link">
        <span class="brand-text font-weight-light">Gestión Udenar</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column">
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>" class="nav-link <?= ($currentUrl == 'dashboard') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header text-muted">BASE DE DATOS</li>

                <?php foreach ($modulos as $m): ?>
                    <li class="nav-item">
                        <a href="<?= BASE_URL . $m ?>" class="nav-link <?= (strpos($currentUrl, $m) === 0) ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-database text-accent"></i>
                            <p><?= ucfirst($m) ?></p>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>
</aside>