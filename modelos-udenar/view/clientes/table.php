<?php if ($result && $result->num_rows > 0): $primerColumna = $fields[0]->name ?? null; ?>
<table class="table table-striped table-hover">
    <thead>
        <tr>
            <?php foreach ($fields as $field): ?>
            <th><?= htmlspecialchars(ucfirst($field->name)) ?></th>
            <?php endforeach; ?>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): $registroId = $row[$primerColumna]; ?>
        <tr>
            <?php foreach ($row as $value): ?>
            <td><?= htmlspecialchars($value) ?></td>
            <?php endforeach; ?>
            <td>
                <button
                    class="btn btn-sm btn-warning"
                    onclick="abrirModalEditar(<?= $registroId ?>)"
                >
                    <i class="fas fa-edit"></i>
                </button>
                <a
                    href="controllers/ClientesController.php?action=delete&id=<?= $registroId ?>"
                    class="btn btn-sm btn-danger"
                    onclick="return confirm('Eliminar registro?');"
                    ><i class="fas fa-trash"></i
                ></a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php else: ?>
<div class="alert alert-info">No se encontraron registros.</div>
<?php endif; ?>
