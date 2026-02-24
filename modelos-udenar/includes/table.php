<?php
require_once 'config/Connectdb.php';

$buscar = isset($_GET['buscar']) ? $_GET['buscar'] : '';

// Consulta filtrada si hay búsqueda
$sql = "SELECT * FROM Clientes";
$params = [];
$types = '';
if ($buscar !== '') {
    $sql .= " WHERE ";
    $sql .= "nombre LIKE ? OR email LIKE ?"; // Cambia 'nombre' y 'email' por las columnas que quieras buscar
    $like = "%$buscar%";
    $params = [$like, $like];
    $types = "ss";
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$fields = $result->fetch_fields();
$primerColumna = $fields[0]->name;
?>
<?php if ($result && $result->num_rows > 0): ?>
<table class="data-table">
    <thead>
        <tr>
            <?php foreach ($fields as $field): ?>
                <th><?php echo htmlspecialchars(ucfirst($field->name)); ?></th>
            <?php endforeach; ?>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <?php $registroId = $row[$primerColumna]; ?>
            <tr>
                <?php foreach ($row as $value): ?>
                    <td><?php echo htmlspecialchars($value ?? ''); ?></td>
                <?php endforeach; ?>
                <td class="actions">
                    <button class="btn-edit" onclick="editarRegistro('<?php echo $registroId; ?>')" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-delete" onclick="eliminarRegistro('<?php echo $registroId; ?>')" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php else: ?>
<div class="empty-message">
    <i class="fas fa-database"></i>
    <p>No se encontraron registros.</p>
</div>
<?php endif; ?>
<?php $stmt->close(); $conn->close(); ?>