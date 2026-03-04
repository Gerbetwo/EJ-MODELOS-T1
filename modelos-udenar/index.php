<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config/connectdb.php';

// Obtener término de búsqueda
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

// Obtener todas las columnas de ventas
$colResult = $conn->query("SHOW COLUMNS FROM ventas");
$columnas = [];
while ($col = $colResult->fetch_assoc()) {
    $columnas[] = $col['Field'];
}

// Construir consulta
$sql = "SELECT * FROM ventas";

if (!empty($buscar)) {
    $buscar_escapado = $conn->real_escape_string($buscar);
    $condiciones = [];
    foreach ($columnas as $col) {
        $condiciones[] = "`$col` LIKE '%$buscar_escapado%'";
    }
    $sql .= " WHERE " . implode(" OR ", $condiciones);
}

// Ejecutar consulta
$result = $conn->query($sql);
if (!$result) {
    die("Error en consulta SQL: " . $conn->error);
}

// Obtener campos para la tabla (si hay registros)
$fields = $result->num_rows > 0 ? $result->fetch_fields() : [];

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

?>
<div id="tablaResultados">
    <?php include 'includes/table.php'; ?>
</div>
<?php
include 'includes/footer.php';
$conn->close();
?>