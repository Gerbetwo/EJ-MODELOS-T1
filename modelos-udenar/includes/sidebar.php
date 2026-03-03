<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="index.php" class="brand-link">
    <span class="brand-text font-weight-light">Gestión Modelo Udenar</span>
  </a>
  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
        <li class="nav-item">
          <a href="index.php?page=dashboard" class="nav-link <?= ($page=='dashboard')?'active':'' ?>">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="index.php?page=clientes" class="nav-link <?= ($page=='clientes')?'active':'' ?>">
            <i class="nav-icon fas fa-users"></i>
            <p>Clientes</p>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</aside>