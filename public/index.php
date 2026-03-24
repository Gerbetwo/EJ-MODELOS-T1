<?php

declare(strict_types=1);

/**
 * FRONT CONTROLLER + COMPOSITION ROOT
 *
 * **Capa:** Presentación (Anillo 4 — punto de entrada)
 *
 * **¿Qué es un Composition Root?**
 * Es el ÚNICO lugar en la aplicación donde se ensamblan las dependencias.
 * Aquí creamos todos los objetos y los "cableamos" entre sí. Ninguna otra
 * clase en la app usa `new` para crear sus dependencias — las recibe inyectadas.
 *
 * Flujo:
 * 1. Cargar autoloader + .env
 * 2. Crear Request (único lugar que toca superglobales)
 * 3. Ensamblar el árbol de dependencias (Composition Root)
 * 4. $response = $router->dispatch($request)
 * 5. Renderizar layout si es necesario
 * 6. $response->send()
 */

// ─── 1. AUTOLOADER + ENV ─────────────────────────────────────
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

ini_set('display_errors', '1');
error_reporting(E_ALL);

session_start();

// ─── 2. IMPORTS ──────────────────────────────────────────────
use App\Application\DTO\Request;
use App\Application\DTO\Response;
use App\Domain\Exception\DatabaseException;
use App\Domain\Exception\HttpException;
use App\Domain\Exception\ValidationException;
use App\Infrastructure\Config\EnvConfig;
use App\Infrastructure\Persistence\PdoConnection;
use App\Infrastructure\Registry\ModelRegistry;
use App\Presentation\Middleware\CsrfMiddleware;
use App\Presentation\Router\Router;
use App\Presentation\View\ViewRenderer;

// ─── 3. TRY-CATCH GLOBAL ────────────────────────────────────
try {
    // 3a. Request Value Object — ÚNICO lugar que toca superglobales
    $request = Request::fromGlobals();

    // 3b. Composition Root — ensamblar dependencias
    $config = new EnvConfig();
    $pdoConnection = new PdoConnection($config);
    $pdo = $pdoConnection->get();

    $modelRegistry = new ModelRegistry(
        modelDirectory: __DIR__ . '/../src/Domain/Entity',
        modelNamespace: 'App\\Domain\\Entity\\',
    );

    $csrf = new CsrfMiddleware();
    $renderer = new ViewRenderer();

    // 3c. Router — recibe todas las dependencias ensambladas
    $router = new Router($pdo, $modelRegistry, $csrf, $renderer);

    // 3d. Despachar la petición → obtener Response
    $response = $router->dispatch($request);

    // 3e. Variables para el layout
    $tableName = $router->getSlug();
    $basePath = '/';
    $moduleSlugs = $router->getAllModuleSlugs();

    // 3f. Si la respuesta necesita el layout maestro, envolverla
    if ($response->needsLayout()) {
        $content = $response->body;
        ob_start();
        include __DIR__ . '/../views/layout/template.phtml';
        $fullHtml = (string) ob_get_clean();

        $response = Response::html($fullHtml, $response->statusCode);
    }

    // 3g. Enviar la respuesta al cliente (ÚNICO echo/header de toda la app)
    $response->send();

} catch (ValidationException $e) {
    Response::json([
        'success' => false,
        'errors'  => $e->getErrors(),
    ], $e->getCode())->send();

} catch (HttpException $e) {
    $statusCode = $e->getStatusCode();
    $errorMessage = $e->getMessage();
    http_response_code($statusCode);

    if (($request ?? null)?->isAjax()) {
        Response::json(['success' => false, 'message' => $errorMessage], $statusCode)->send();
    } else {
        include __DIR__ . '/../views/error.phtml';
    }

} catch (DatabaseException $e) {
    error_log('[DatabaseException] ' . $e->getMessage());
    $statusCode = 500;
    $errorMessage = 'Error interno del servidor. Por favor, intente más tarde.';
    http_response_code(500);
    include __DIR__ . '/../views/error.phtml';

} catch (\Throwable $e) {
    error_log('[UnhandledException] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    $statusCode = 500;
    $errorMessage = 'Ha ocurrido un error inesperado.';
    http_response_code(500);
    include __DIR__ . '/../views/error.phtml';
}
