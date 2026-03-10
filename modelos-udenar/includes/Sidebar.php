<?php
// includes/Sidebar.php
$inspector = new DatabaseInspector($conn);
$tables = $inspector->getTables();
?>
<aside class="main-sidebar sidebar-dark-primary bg-transparent elevation-4 sidebar-mini">
    <a href="<?= BASE_URL ?>" class="brand-link">
        <img src="<?= BASE_URL ?>favicon.ico" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: 0.9" />
        <span class="brand-text font-weight-light">Gestión Udenar</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column">
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>" class="nav-link <?= (!isset($_GET['url']) || $_GET['url'] == 'dashboard') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header text-muted">BASE DE DATOS</li>

                <?php foreach ($tables as $t): ?>
                    <li class="nav-item">
                        <a href="<?= BASE_URL . strtolower($t['name']) ?>"
                            class="nav-link <?= (strpos(($_GET['url'] ?? ''), strtolower($t['name'])) !== false) ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-database text-accent"></i>
                            <p><?= ucfirst($t['name']) ?></p>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>
</aside>