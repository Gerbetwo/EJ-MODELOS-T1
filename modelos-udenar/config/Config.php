<?php
// config/Config.php
error_reporting(E_ALL);
// --- 1. CARGADOR DE VARIABLES .ENV ---
function loadEnv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $_ENV[trim($parts[0])] = trim($parts[1]);
        }
    }
}

// Ejecutamos la carga (ajustando la ruta a la raíz del proyecto)
loadEnv(__DIR__ . '/../.env');

// --- 2. DEFINICIÓN DE CONSTANTES (Credenciales y Rutas) ---
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'db_modelos_udenar');

// URL Base para assets (CSS/JS)
define('BASE_URL', '/modelos-udenar/');

// --- 3. AUTOLOADER PSR-4 (El fin de los "includes" manuales) ---
spl_autoload_register(function ($className) {
    // Carpetas donde el sistema buscará las clases automáticamente
    $directories = [
        'controllers',
        'models',
        'config',
        'includes'
    ];

    foreach ($directories as $dir) {
        // Construimos la ruta absoluta: /ruta/al/proyecto/carpeta/Clase.php
        $file = __DIR__ . '/../' . $dir . '/' . $className . '.php';
        
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});