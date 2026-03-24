<?php

declare(strict_types=1);

namespace App\Application\DTO;

/**
 * Value Object inmutable que encapsula una petición HTTP.
 *
 * **Capa:** Aplicación (Anillo 2)
 *
 * **¿Por qué un Value Object para el Request?**
 *
 * ANTES (superglobales esparcidas por todo el código):
 * ```php
 * // En Router.php
 * $url = $_GET['url'] ?? 'dashboard';
 * // En Router::handleStore()
 * if ($_SERVER['REQUEST_METHOD'] !== 'POST') { ... }
 * // En GenericController::store()
 * $controller->store($_POST);
 * ```
 *
 * AHORA (un solo punto de creación):
 * ```php
 * // En index.php (ÚNICO lugar que toca superglobales)
 * $request = Request::fromGlobals();
 * // Todo el resto recibe Request por inyección
 * $router->dispatch($request);
 * ```
 *
 * **Beneficios:**
 * 1. **Testabilidad:** En tests creas `new Request('GET', '/clients', ...)` sin tocar superglobales.
 * 2. **Inmutabilidad:** `readonly` garantiza que nadie modifica el request en tránsito.
 * 3. **Encapsulación:** Si mañana usas un servidor Swoole (sin superglobales), solo cambias `fromGlobals()`.
 *
 * En Symfony, esto equivale a `Symfony\Component\HttpFoundation\Request`.
 * En PSR-7, sería `Psr\Http\Message\ServerRequestInterface`.
 */
final readonly class Request
{
    /**
     * @param string               $method    Método HTTP (GET, POST, DELETE)
     * @param string               $uri       URI de la petición
     * @param array<string, mixed> $query     Parámetros GET
     * @param array<string, mixed> $body      Datos POST
     * @param array<string, mixed> $server    Variables del servidor
     */
    public function __construct(
        public string $method,
        public string $uri,
        public array $query = [],
        public array $body = [],
        public array $server = [],
    ) {
    }

    /**
     * Crea un Request a partir de los superglobales de PHP.
     *
     * Este es el ÚNICO lugar en toda la aplicación donde se acceden los
     * superglobales. Se llama una sola vez en index.php (Composition Root).
     */
    public static function fromGlobals(): self
    {
        return new self(
            method: $_SERVER['REQUEST_METHOD'] ?? 'GET',
            uri: $_GET['url'] ?? 'dashboard',
            query: $_GET,
            body: $_POST,
            server: $_SERVER,
        );
    }

    /**
     * Verifica si la petición es AJAX (XMLHttpRequest).
     */
    public function isAjax(): bool
    {
        return !empty($this->server['HTTP_X_REQUESTED_WITH'])
            && strtolower((string) $this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Verifica si la petición es POST.
     */
    public function isPost(): bool
    {
        return strtoupper($this->method) === 'POST';
    }
}
