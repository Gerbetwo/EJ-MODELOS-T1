<?php
// includes/sidebar.php
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="index.php" class="brand-link">
    <img src="adminlte/dist/assets/img/AdminLTELogo.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light">Gestión Udenar</span>
  </a>

  <div class="sidebar">
    <nav class="mt-2 pt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview">
        <li class="nav-item">
          <a href="index.php" class="nav-link <?= ($_GET['module'] ?? 'dashboard') === 'dashboard' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="index.php?module=clientes" class="nav-link <?= ($_GET['module'] ?? '') === 'clientes' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-users"></i>
            <p>Clientes</p>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</aside>