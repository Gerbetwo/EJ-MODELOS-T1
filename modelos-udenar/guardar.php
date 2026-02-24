<?php
// guardar.php
require_once 'config/Connectdb.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Obtener las columnas (excepto la primera, ID autoincremental)
$resultCol = $conn->query("SHOW COLUMNS FROM Clientes");
$columnas = [];
$primeraColumna = null;
$primero = true;
while ($col = $resultCol->fetch_assoc()) {
    if ($primero) {
        $primeraColumna = $col['Field'];
        $primero = false;
        continue;
    }
    $columnas[] = $col['Field'];
}

// Filtrar solo los campos enviados que coinciden con las columnas
$campos = array_filter(array_keys($_POST), function($campo) use ($columnas) {
    return in_array($campo, $columnas);
});

if (empty($campos)) {
    die('No hay datos para insertar');
}

// Construir la consulta: INSERT INTO Clientes (campo1, campo2) VALUES (?, ?)
$placeholders = implode(', ', array_fill(0, count($campos), '?'));
$sql = "INSERT INTO Clientes (" . implode(', ', $campos) . ") VALUES ($placeholders)";

$stmt = $conn->prepare($sql);
$tipos = str_repeat('s', count($campos)); // Asume todos string, ajusta si hay otros tipos
$valores = [];
foreach ($campos as $campo) {
    $valores[] = $_POST[$campo];
}
$stmt->bind_param($tipos, ...$valores);

if ($stmt->execute()) {
    header('Location: index.php?mensaje=creado');
} else {
    echo "Error al guardar: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>