<?php
$esEditar = isset($row); ?>
<form
    action="<?= $esEditar
        ? '../../controllers/ClientesController.php?action=edit&id=' . $row['id']
        : '../../controllers/ClientesController.php?action=create' ?>"
    method="POST"
>
    <?php
    $cols = $row ?? $conn->query('SHOW COLUMNS FROM Clientes');
    foreach ($cols as $campo => $valor):
        if ($campo === 'id') {
            continue;
        } ?>
    <div class="form-group mb-2">
        <label><?= ucfirst($campo) ?></label>
        <input
            type="text"
            name="<?= $campo ?>"
            value="<?= $esEditar ? htmlspecialchars($valor) : '' ?>"
            class="form-control"
            required
        />
    </div>
    <?php
    endforeach;
    ?>

    <div class="mt-3">
        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times"></i> Cancelar
        </button>
    </div>
</form>
