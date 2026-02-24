<?php
require_once 'config/Connectdb.php';

// Obtener término de búsqueda desde la URL
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

// Incluir la plantilla de la tabla (sin header ni footer)
include 'includes/table.php';

$conn->close();
?>