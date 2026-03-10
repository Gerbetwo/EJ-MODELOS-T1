<?php
// includes/sidebar.php
$inspector = new DatabaseInspector($conn);
$tables = $inspector->getTables();
?>
<aside class="main-sidebar sidebar-dark-primary bg-transparent elevation-4 sidebar-mini">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link">
        <img
            src="../../favicon.ico"
            alt="Logo"
            class="brand-image img-circle elevation-3"
            style="opacity: 0.9" />
        <span class="brand-text font-weight-light">Gestión Udenar</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column">
                <li class="nav-item">
                    <a href="index.php" class="nav-link <?= !isset($_GET['table']) ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header text-muted">BASE DE DATOS</li>

                <?php foreach ($tablas as $t): ?>
                    <li class="nav-item">
                        <a href="index.php?table=<?= $t['name'] ?>"
                            class="nav-link <?= (($_GET['table'] ?? '') === $t['name']) ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-database text-accent"></i>
                            <p><?= ucfirst($t['name']) ?></p>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>
</aside>