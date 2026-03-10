<?php
// view/clientes/Form.php
// props: $columnsMeta, $rowData (si es editar)

$isEdit = isset($rowData);
// URL Limpia: /modelos-udenar/tabla/create  o  /modelos-udenar/tabla/update
$actionUrl = BASE_URL . $tableName . ($isEdit ? '/update' : '/create');
?>

<form action="<?= $actionUrl ?>" method="POST" class="p-3">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= $rowData['id'] ?>">
    <?php endif; ?>

    <div class="row">
        <?php foreach ($columnsMeta as $col): 
            if ($col['name'] === 'id') continue; // No mostramos el ID
            
            // Detectar el tipo de input según la base de datos
            $type = 'text';
            if (strpos($col['type'], 'int') !== false) $type = 'number';
            if (strpos($col['type'], 'date') !== false) $type = 'date';
        ?>
            <div class="col-md-6 form-group">
                <label class="text-accent"><?= ucfirst($col['name']) ?></label>
                <input 
                    type="<?= $type ?>" 
                    name="<?= $col['name'] ?>" 
                    value="<?= $isEdit ? htmlspecialchars($rowData[$col['name']]) : '' ?>"
                    class="form-control form-control-custom"
                    required
                >
            </div>
        <?php endforeach; ?>
    </div>

    <div class="text-right mt-3">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-brand">
            <?= $isEdit ? 'Actualizar Cambios' : 'Guardar Registro' ?>
        </button>
    </div>
</form>