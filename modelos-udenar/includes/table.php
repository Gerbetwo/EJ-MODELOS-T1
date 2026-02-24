<?php
// Detectamos automáticamente cuál es la columna que hace de ID (usualmente la primera)
// Esto evita que el código falle si tu tabla no tiene una columna llamada 'id'
$columnas = $fields; 
$primerColumna = $columnas[0]->name; 
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
                <?php 
                    // Usamos el valor de la primera columna como ID para los botones
                    $registroId = $row[$primerColumna]; 
                ?>
                <tr>
                    <?php foreach ($row as $value): ?>
                        <td><?php echo htmlspecialchars($value ?? ''); ?></td>
                    <?php endforeach; ?>
                    
                    <td class="actions">
                        <button class="btn-edit" 
                                onclick="editarRegistro('<?php echo $registroId; ?>')" 
                                title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        
                        <button class="btn-delete" 
                                onclick="eliminarRegistro('<?php echo $registroId; ?>')" 
                                title="Eliminar">
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
        <p>No se encontraron registros en la tabla consultada.</p>
    </div>
<?php endif; ?>