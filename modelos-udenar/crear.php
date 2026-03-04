<?php
require_once 'config/connectdb.php';

// Obtener columnas
$resultCol = $conn->query("SHOW COLUMNS FROM ventas");
$columnas = [];
while ($col = $resultCol->fetch_assoc()) $columnas[] = $col;

$primeraColumna = $columnas[0]['Field'];
?>
<form action="guardar.php" method="POST">
    <?php foreach ($columnas as $col): ?>
        <?php if ($col['Field'] == $primeraColumna) continue; ?>
        <div class="form-group">
            <label for="<?php echo $col['Field']; ?>"><?php echo ucfirst($col['Field']); ?></label>
            <input type="text" 
                   id="<?php echo $col['Field']; ?>" 
                   name="<?php echo $col['Field']; ?>" 
                   placeholder="Ingrese <?php echo $col['Field']; ?>"
                   <?php echo ($col['Null'] == 'NO') ? 'required' : ''; ?>>
        </div>
    <?php endforeach; ?>

    <div style="display:flex; gap:10px; margin-top:15px;">
        <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Guardar</button>
        <button type="button" class="btn-cancel" onclick="cerrarModalCrear()"><i class="fas fa-times"></i> Cancelar</button>
    </div>
</form>
<?php $conn->close(); ?>