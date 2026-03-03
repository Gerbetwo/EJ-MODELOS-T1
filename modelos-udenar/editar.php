<?php
require_once 'config/connectdb.php';

$id = intval($_GET['id']); 

$sql = "SELECT * FROM Clientes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();
?>

<form action="actualizar.php" method="POST">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    
    <?php foreach ($row as $campo => $valor): ?>
        <?php if ($campo !== 'id'): ?>
        <div class="form-group">
            <label><?php echo ucfirst($campo); ?></label>
            <input type="text" name="<?php echo $campo; ?>" value="<?php echo htmlspecialchars($valor); ?>" required>
        </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <div style="display: flex; gap: 10px; margin-top: 15px;">
        <button type="submit" class="btn-submit">Guardar</button>
        <button type="button" class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
    </div>
</form>