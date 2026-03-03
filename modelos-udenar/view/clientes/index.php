<div class="content-header">
  <h1 class="m-0">Clientes</h1>
</div>

<div class="content">
  <?php include '../../includes/alert.php'; ?>

  <div class="mb-3">
    <button class="btn btn-primary" onclick="abrirModalCrear()">Nuevo Cliente <i class="fas fa-plus-circle"></i></button>
    <form class="d-inline ms-3" method="GET" action="index.php">
        <input type="hidden" name="page" value="clientes">
        <input type="text" name="buscar" placeholder="Buscar..." value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>">
        <button class="btn btn-secondary"><i class="fas fa-search"></i></button>
    </form>
  </div>

  <?php include 'table.php'; ?>
</div>

<!-- Modales -->
<div id="modalCrear" class="modal fade" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" id="modal-body-crear">
      <!-- Contenido cargado vía AJAX -->
    </div>
  </div>
</div>

<div id="modalEditar" class="modal fade" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" id="modal-body-editar">
      <!-- Contenido cargado vía AJAX -->
    </div>
  </div>
</div>