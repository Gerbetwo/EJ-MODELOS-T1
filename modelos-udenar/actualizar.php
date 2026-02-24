<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// actualizar.php
require_once 'config/Connectdb.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Obtener el ID
$id = intval($_POST['id']);

// Preparar la consulta dinámica
// Excluimos 'id' del array de campos a actualizar
$campos = array_filter(array_keys($_POST), function($campo) {
    return $campo !== 'id';
});

if (empty($campos)) {
    die('No hay datos para actualizar');
}

// Construir la parte SET de la consulta: campo1=?, campo2=?, ...
$set = implode('=?, ', $campos) . '=?';
$sql = "UPDATE Clientes SET $set WHERE id = ?";

// Preparar la sentencia
$stmt = $conn->prepare($sql);

// Crear los tipos de datos (asumimos que todos son strings excepto el ID que es entero)
$tipos = str_repeat('s', count($campos)) . 'i'; // 's' para cada campo, 'i' para el ID

// Crear array de parámetros
$parametros = [];
foreach ($campos as $campo) {
    $parametros[] = $_POST[$campo];
}
$parametros[] = $id; // añadimos el ID al final

// Vincular parámetros dinámicamente
$stmt->bind_param($tipos, ...$parametros);

if ($stmt->execute()) {
    echo "ok"; // Para AJAX
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>