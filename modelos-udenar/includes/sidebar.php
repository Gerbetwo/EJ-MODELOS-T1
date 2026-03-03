<?php
// includes/sidebar.php
?>
<aside class="main-sidebar sidebar-dark-primary bg-transparent elevation-4 sidebar-mini">
  <!-- Brand Logo -->
  <a href="index.php" class="brand-link">
    <img src="adminlte/dist/assets/img/AdminLTELogo.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .9">
    <span class="brand-text font-weight-light">Gestión Udenar</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" data-accordion="false">
        <li class="nav-item">
          <a href="index.php" class="nav-link <?= ($_GET['module'] ?? 'dashboard') === 'dashboard' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>
        <li class="nav-item has-treeview <?= ($_GET['module'] ?? '') === 'clientes' ? 'menu-open' : '' ?>">
          <a href="#" class="nav-link <?= ($_GET['module'] ?? '') === 'clientes' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-users"></i>
            <p>
              Clientes
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="index.php?module=clientes" class="nav-link <?= ($_GET['module'] ?? '') === 'clientes' && !isset($_GET['sub']) ? 'active' : '' ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Listado</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="index.php?module=clientes&sub=nuevo" class="nav-link <?= ($_GET['module'] ?? '') === 'clientes' && ($_GET['sub'] ?? '') === 'nuevo' ? 'active' : '' ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Nuevo</p>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
  </div>
</aside>