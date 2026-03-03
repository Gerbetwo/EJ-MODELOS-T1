<?php
// view/clientes/index.php
// Variables esperadas en contexto: $clientes (array), $columnsMeta (array)
$cols = $columnsMeta; // array de metadatos de columnas (Field, Type, ...)
?>
<section class="content-header mb-3">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-users"></i> Clientes</h2>
    <div>
      <a href="index.php?module=clientes" class="btn btn-outline-secondary me-2">Recargar</a>
      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrear">Nuevo</button>
    </div>
  </div>
</section>

<section class="content">
  <div class="container-fluid">
    <form class="d-flex mb-3" method="GET" action="index.php">
      <input type="hidden" name="module" value="clientes">
      <input class="form-control me-2" type="search" placeholder="Buscar..." name="buscar" value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>">
      <button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i></button>
    </form>

    <?php if (!empty($clientes)): ?>
    <div class="table-responsive">
      <table class="table table-striped table-hover table-bordered">
        <thead class="table-light">
          <tr>
            <?php foreach (array_keys($clientes[0]) as $h): ?>
              <th><?= htmlspecialchars(ucfirst($h)) ?></th>
            <?php endforeach; ?>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($clientes as $row): ?>
            <tr>
              <?php foreach ($row as $val): ?>
                <td><?= htmlspecialchars($val) ?></td>
              <?php endforeach; ?>
              <td>
                <button class="btn btn-sm btn-primary me-1 btn-edit" data-id="<?= $row['id'] ?>" data-bs-toggle="modal" data-bs-target="#modalEditar"><i class="fas fa-edit"></i></button>
                <a href="index.php?module=clientes&action=delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Confirmar eliminación?')"><i class="fas fa-trash"></i></a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
      <div class="alert alert-info">No se encontraron registros.</div>
    <?php endif; ?>
  </div>
</section>

<!-- Modal Crear (genera inputs según columnas) -->
<div class="modal fade" id="modalCrear" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="index.php?module=clientes&action=create">
        <div class="modal-header">
          <h5 class="modal-title">Nuevo Cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <?php
            // generamos inputs para cada columna excepto la primera (id autoincrement)
            $first = true;
            foreach ($cols as $col):
                $name = $col['Field'];
                if ($first) { $first = false; continue; }
            ?>
            <div class="col-md-6 mb-3">
              <label class="form-label"><?= ucfirst($name) ?></label>
              <input class="form-control" name="<?= htmlspecialchars($name) ?>" type="text" required>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Editar (rellenado por JS) -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="formEditar" method="POST" action="index.php?module=clientes&action=update">
        <div class="modal-header">
          <h5 class="modal-title">Editar Cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="edit_id">
          <div class="row" id="edit_fields">
            <!-- campos se insertan por JS -->
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>