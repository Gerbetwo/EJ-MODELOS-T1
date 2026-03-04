<?php
require_once 'config/connectdb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Obtener columnas reales de la tabla ventas
    $res = $conn->query("SHOW COLUMNS FROM ventas");
    $columnasTabla = [];
    while($col = $res->fetch_assoc()) {
        // Ignoramos campos automáticos
        if($col['Extra'] == 'auto_increment' || $col['Default'] == 'current_timestamp()') continue;
        $columnasTabla[] = $col['Field'];
    }

    // 2. Filtrar lo que llegó por POST que sí esté en la tabla
    $campos = [];
    $valores = [];
    foreach ($columnasTabla as $col) {
        if (isset($_POST[$col])) {
            $campos[] = $col;
            $valores[] = $_POST[$col];
        }
    }

    // 3. Construir SQL Dinámico
    $colsSQL = implode(", ", $campos);
    $placeholders = implode(", ", array_fill(0, count($campos), "?"));
    $sql = "INSERT INTO ventas ($colsSQL) VALUES ($placeholders)";

    $stmt = $conn->prepare($sql);
    $tipos = str_repeat("s", count($valores)); // MySQL acepta strings para números
    $stmt->bind_param($tipos, ...$valores);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();