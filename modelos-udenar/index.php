<?php
require_once 'config/Connectdb.php';

// Obtener término de búsqueda
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

// Construir consulta con filtro si hay búsqueda
if (!empty($buscar)) {
    $buscar_escapado = $conn->real_escape_string($buscar);
    
    // Obtener todas las columnas de la tabla Clientes
    $colResult = $conn->query("SHOW COLUMNS FROM Clientes");
    $columnas = [];
    while ($col = $colResult->fetch_assoc()) {
        $columnas[] = $col['Field'];
    }
    
    // Crear condiciones WHERE para cada columna
    $condiciones = [];
    foreach ($columnas as $col) {
        $condiciones[] = "`$col` LIKE '%$buscar_escapado%'";
    }
    $where = "WHERE " . implode(" OR ", $condiciones);
    $sql = "SELECT * FROM Clientes $where";
} else {
    $sql = "SELECT * FROM Clientes";
}

$result = $conn->query($sql);
$fields = $result->fetch_fields();

include 'includes/header.php';

// Mostrar mensajes de éxito
if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje'];
    $clase = $texto = '';
    if ($mensaje == 'actualizado') {
        $clase = 'success';
        $texto = 'Registro actualizado correctamente.';
    } elseif ($mensaje == 'eliminado') {
        $clase = 'info';
        $texto = 'Registro eliminado correctamente.';
    } elseif ($mensaje == 'creado') {
        $clase = 'success';
        $texto = 'Nuevo cliente creado correctamente.';
    }
    if ($texto) {
        echo "<div class='alert alert-$clase'>$texto</div>";
    }
}

include 'includes/table.php';
include 'includes/footer.php';
$conn->close();
?>