<?php
// index.php - Front Controller
ini_set('display_errors', 1);
error_reporting(E_ALL);
// 1. Iniciador del sistema (Carga .env, Constantes y Autoloader)
require_once 'config/Config.php';

// 2. Iniciar conexión
$db = new Database();
$conn = $db->getConnection();

// 3. Iniciar el Componente Router para procesar la petición
$router = new Router($conn);
$content = $router->resolve();

// 4. Cargar el Layout Maestro
include 'includes/Template.php';