<?php

declare(strict_types=1);

/**
 * FRONT CONTROLLER — Punto de entrada único de la aplicación.
 *
 * **¿Qué es un Front Controller?**
 * Es el ÚNICO archivo PHP al que Apache tiene acceso directo. Todas las
 * peticiones HTTP pasan por aquí gracias al .htaccess. Su trabajo es:
 * 1. Cargar el autoloader de Composer
 * 2. Cargar el archivo .env
 * 3. Crear la conexión a la BD
 * 4. Despachar la petición al Router
 * 5. Capturar cualquier excepción y mostrar una respuesta limpia
 *
 * **¿Por qué un try-catch global?**
 * En la versión anterior, los errores se manejaban con `die()` en cada lugar.
 * Esto causaba pantallas blancas o mensajes crípticos. Con un try-catch global,
 * CUALQUIER error no manejado llega aquí y se muestra con una vista amigable.
 *
 * **¿Por qué la carpeta se llama public/?**
 * Solo esta carpeta es accesible vía web (DocumentRoot de Apache). Los archivos
 * de `src/`, `views/`, `.env` etc. quedan FUERA del acceso web, lo cual es
 * una práctica de seguridad fundamental. Si alguien intenta acceder a
 * http://tu-app/.env, Apache retorna 404 porque .env no está en public/.
 */

// ─────────────────────────────────────────────────────────────
// 1. AUTOLOADER DE COMPOSER
// ─────────────────────────────────────────────────────────────
// Composer genera un autoloader PSR-4 que mapea namespaces a directorios.
// App\Model\Cliente → src/Model/Cliente.php (automáticamente).
require_once __DIR__ . '/../vendor/autoload.php';

// ─────────────────────────────────────────────────────────────
// 2. VARIABLES DE ENTORNO (.env)
// ─────────────────────────────────────────────────────────────
// vlucas/phpdotenv lee el archivo .env y carga las variables en $_ENV.
// Es el estándar de facto para configuración en PHP (lo usan Laravel, Symfony, etc.)
//
// **¿Por qué no parsear .env manualmente como antes?**
// Porque phpdotenv maneja edge cases que tu parser no cubría:
// - Valores con espacios y comillas: DB_PASS="mi contraseña con espacios"
// - Variables que referencian otras: DB_URL="${DB_HOST}:${DB_PORT}"
// - Validación de variables requeridas
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// ─────────────────────────────────────────────────────────────
// 3. CONFIGURACIÓN DE ERRORES
// ─────────────────────────────────────────────────────────────
// En desarrollo mostramos errores. En producción, esto debería ser 0.
// TODO: Mover esto a una variable APP_DEBUG en .env
ini_set('display_errors', '1');
error_reporting(E_ALL);

// ─────────────────────────────────────────────────────────────
// 4. CONSTANTES GLOBALES
// ─────────────────────────────────────────────────────────────
define('BASE_URL', '/');

// ─────────────────────────────────────────────────────────────
// 5. TRY-CATCH GLOBAL — Manejador de excepciones centralizado
// ─────────────────────────────────────────────────────────────
use App\Exception\DatabaseException;
use App\Exception\HttpException;
use App\Exception\ValidationException;
use App\Infrastructure\Database;
use App\Router\Router;

try {
    // 5a. Crear conexión a BD (lazy — no se conecta hasta que se necesite)
    $database = Database::fromEnv();
    $pdo = $database->getConnection();

    // 5b. Crear Router y resolver la petición
    $router = new Router($pdo, BASE_URL);
    $content = $router->resolve();

    // 5c. Variables para el template
    $tableName = $router->getSlug();
    $basePath = $router->getBasePath();
    $moduleSlugs = $router->getAllModuleSlugs();

    // 5d. Si resolve() ya envió headers (AJAX/redirect), no renderizar template
    if (headers_sent() || $content === '') {
        exit;
    }

    // 5e. Renderizar layout maestro con el contenido
    include __DIR__ . '/../views/layout/template.phtml';

} catch (ValidationException $e) {
    // Error de validación — responder con JSON 422 para AJAX
    http_response_code($e->getCode());
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'errors'  => $e->getErrors(),
    ], JSON_THROW_ON_ERROR);

} catch (HttpException $e) {
    // Error HTTP (404, 500, etc.) — mostrar vista de error amigable
    $statusCode = $e->getStatusCode();
    $errorMessage = $e->getMessage();
    http_response_code($statusCode);

    // Si es una petición AJAX, responder con JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    ) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => $errorMessage]);
    } else {
        include __DIR__ . '/../views/error.phtml';
    }

} catch (DatabaseException $e) {
    // Error de BD — log interno + vista genérica de error
    // En producción, aquí logearías a un archivo o servicio externo
    error_log('[DatabaseException] ' . $e->getMessage());

    $statusCode = 500;
    $errorMessage = 'Error interno del servidor. Por favor, intente más tarde.';
    http_response_code(500);
    include __DIR__ . '/../views/error.phtml';

} catch (\Throwable $e) {
    // Cualquier otro error no previsto
    error_log('[UnhandledException] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

    $statusCode = 500;
    $errorMessage = 'Ha ocurrido un error inesperado.';
    http_response_code(500);
    include __DIR__ . '/../views/error.phtml';
}
