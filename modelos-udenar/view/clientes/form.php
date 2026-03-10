<?php $esEditar = isset($row); ?>

<form action="index.php?module=clientes&action=<?= $esEditar ? 'update' : 'create' ?>" method="POST">
    <?php if($esEditar): ?>
        <input type="hidden" name="id" value="<?= $row['id'] ?>">
    <?php endif; ?>

    <div class="row">
    <?php 
    // Si no hay row (creación), pedimos las columnas al controlador
    $fields = $row ?? array_fill_keys(array_column($columnsMeta, 'Field'), '');
    
    foreach ($fields as $campo => $valor):
        if ($campo === 'id') continue; ?>
        
        <div class="col-md-6 form-group">
            <label class="text-accent-custom font-weight-bold">
                <?= ucfirst(str_replace('_', ' ', $campo)) ?>
            </label>
            <input
                type="text"
                name="<?= $campo ?>"
                value="<?= htmlspecialchars($valor) ?>"
                class="form-control form-control-custom bg-glass-dark-custom"
                placeholder="Ingrese <?= $campo ?>"
                required />
        </div>
    <?php endforeach; ?>
    </div>

    <hr class="border-glass">
    
    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-outline-secondary mr-2" data-dismiss="modal">
            <i class="fas fa-times mr-1"></i>Cancelar
        </button>
        <button type="submit" class="btn btn-primary-custom px-4">
            <i class="fas fa-save mr-1"></i><?= $esEditar ? 'Actualizar' : 'Guardar' ?> Cliente
        </button>
    </div>
</form>