<?php
// eliminar.php
require_once 'config/connectdb.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('ID no proporcionado');
}

// Obtener el nombre de la primera columna de la tabla Cliente
$resultCol = $conn->query("SHOW COLUMNS FROM Clientes");
$firstCol = $resultCol->fetch_assoc()['Field'];

$id = $conn->real_escape_string($_GET['id']);

$sql = "DELETE FROM Clientes WHERE $firstCol = '$id'";
if ($conn->query($sql)) {
    header('Location: index.php?mensaje=eliminado');
} else {
    echo "Error al eliminar: " . $conn->error;
}

$conn->close();
?>