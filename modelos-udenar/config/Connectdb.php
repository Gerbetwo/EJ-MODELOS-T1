<?php
// config/database.php

// 1. Cargamos el archivo de secretos desde la ruta absoluta de Linux
// Usamos @ para que si el archivo no existe, no muestre la ruta en el error
if (!@include('/opt/lampp/htdocs/modelos-udenar/config/config.php')) {
    die("Error crítico: No se pudo cargar la configuración de seguridad.");
}

$host = 'localhost';
$user = 'Gerbert';
$password = DB_PASS; // Esta constante viene del archivo include
$dbname = 'db_modelos_udenar'; // Asegúrate que sea guion bajo como en tu error anterior
// 2. Intentar la conexión
$conn = new mysqli($host, $user, $password, $dbname);

// 3. Verificar errores
if ($conn->connect_error) {
    // En producción, mejor usa: die("Error de conexión al servidor.");
    die("Error de conexión (" . $conn->connect_errno . "): " . $conn->connect_error);
}

// Opcional: Configurar el set de caracteres para evitar problemas con tildes
$conn->set_charset("utf8mb4");
?>