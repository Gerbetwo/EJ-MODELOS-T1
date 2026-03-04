<?php
require_once 'config/connectdb.php';

if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $sql = "DELETE FROM ventas WHERE id = '$id'";
    
    if ($conn->query($sql)) {
        header('Location: index.php?mensaje=eliminado');
    } else {
        echo "Error: " . $conn->error;
    }
}
$conn->close();