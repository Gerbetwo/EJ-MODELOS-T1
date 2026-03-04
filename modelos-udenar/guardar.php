<?php
// guardar.php
require_once 'config/connectdb.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Método no permitido';
    echo json_encode($response);
    exit;
}

// Obtener las columnas (excepto la primera, ID autoincremental)
$resultCol = $conn->query("SHOW COLUMNS FROM ventas");
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
    $response['message'] = 'No hay datos para insertar';
    echo json_encode($response);
    exit;
}

// Construir la consulta
$placeholders = implode(', ', array_fill(0, count($campos), '?'));
$sql = "INSERT INTO ventas (" . implode(', ', $campos) . ") VALUES ($placeholders)";

$stmt = $conn->prepare($sql);
$tipos = str_repeat('s', count($campos));
$valores = [];
foreach ($campos as $campo) {
    $valores[] = $_POST[$campo];
}
$stmt->bind_param($tipos, ...$valores);

if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = 'Cliente creado correctamente';
} else {
    $response['message'] = 'Error al guardar: ' . $stmt->error;
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
exit;