<?php
require_once 'config/connectdb.php';

$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

// Obtener columnas
$colResult = $conn->query("SHOW COLUMNS FROM Clientes");
$columnas = [];
while ($col = $colResult->fetch_assoc()) {
    $columnas[] = $col['Field'];
}

// Construir consulta
$sql = "SELECT * FROM Clientes";
if (!empty($buscar)) {
    $buscar_escapado = $conn->real_escape_string($buscar);
    $condiciones = [];
    foreach ($columnas as $col) {
        $condiciones[] = "`$col` LIKE '%$buscar_escapado%'";
    }
    $sql .= " WHERE " . implode(" OR ", $condiciones);
}

$result = $conn->query($sql);
$fields = $result->num_rows > 0 ? $result->fetch_fields() : [];
$primerColumna = $fields[0]->name ?? null; // <-- AÑADE ESTO

include 'includes/table.php';
$conn->close();
?>