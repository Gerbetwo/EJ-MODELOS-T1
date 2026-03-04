<?php
require_once 'config/connectdb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    
    // 1. Obtener columnas (excepto ID y fecha)
    $res = $conn->query("SHOW COLUMNS FROM ventas");
    $updates = [];
    $valores = [];
    
    while($col = $res->fetch_assoc()) {
        $campo = $col['Field'];
        if($campo == 'id' || $campo == 'fecha_venta') continue;
        
        if(isset($_POST[$campo])) {
            $updates[] = "$campo = ?";
            $valores[] = $_POST[$campo];
        }
    }

    // 2. Construir SQL
    $sql = "UPDATE ventas SET " . implode(", ", $updates) . " WHERE id = ?";
    $valores[] = $id; // El ID va al final para el WHERE
    
    $stmt = $conn->prepare($sql);
    $tipos = str_repeat("s", count($valores) - 1) . "i"; 
    $stmt->bind_param($tipos, ...$valores);

    if ($stmt->execute()) {
        echo "ok";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();