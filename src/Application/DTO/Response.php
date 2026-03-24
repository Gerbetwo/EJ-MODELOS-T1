<?php

declare(strict_types=1);

namespace App\Application\DTO;

/**
 * Value Object inmutable que encapsula una respuesta HTTP.
 *
 * **Capa:** Aplicación (Anillo 2)
 *
 * **¿Por qué un Value Object para el Response?**
 *
 * ANTES (echo + header esparcidos por todo el Router):
 * ```php
 * header('Content-Type: application/json');
 * echo json_encode($data);
 * return '';  // ← retorna string vacío como hack
 * ```
 *
 * AHORA (respuesta estructurada):
 * ```php
 * return Response::json($data);  // ← limpio y testeable
 * ```
 *
 * **Beneficios:**
 * 1. El Router/Controller RETORNA un Response en vez de hacer echo/header.
 * 2. El Front Controller llama `$response->send()` como último paso.
 * 3. En tests puedes inspeccionar el Response sin capturar output buffers.
 *
 * En Symfony, esto equivale a `Symfony\Component\HttpFoundation\Response`.
 */
final readonly class Response
{
    /**
     * @param int                  $statusCode Código HTTP
     * @param string               $body       Cuerpo de la respuesta
     * @param array<string, string> $headers    Headers HTTP
     */
    public function __construct(
        public int $statusCode,
        public string $body,
        public array $headers = [],
    ) {
    }

    /**
     * Envía la respuesta al cliente.
     *
     * Este es el ÚNICO lugar donde se hace echo y header() en la app.
     * Se llama una sola vez al final de index.php.
     */
    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        echo $this->body;
    }

    /**
     * Indica si esta respuesta necesita renderizar el layout maestro.
     */
    public function needsLayout(): bool
    {
        // Las respuestas JSON, redirects, y errores no necesitan layout
        $contentType = $this->headers['Content-Type'] ?? '';
        $isRedirect = $this->statusCode >= 300 && $this->statusCode < 400;
        $isFragment = isset($this->headers['X-Fragment']) && $this->headers['X-Fragment'] === '1';

        return !$isRedirect
            && !$isFragment
            && !str_contains($contentType, 'application/json')
            && $this->body !== '';
    }

    // ─────────────────────────────────────────────────────────
    // Factory Methods — nombresExplícitos > new Response(...)
    // ─────────────────────────────────────────────────────────

    /**
     * Respuesta HTML estándar (se envuelve en el layout maestro).
     */
    public static function html(string $body, int $statusCode = 200): self
    {
        return new self(
            statusCode: $statusCode,
            body: $body,
            headers: ['Content-Type' => 'text/html; charset=utf-8'],
        );
    }

    /**
     * Respuesta JSON (para AJAX).
     *
     * @param array<string, mixed> $data Datos a serializar
     */
    public static function json(array $data, int $statusCode = 200): self
    {
        return new self(
            statusCode: $statusCode,
            body: json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            headers: ['Content-Type' => 'application/json; charset=utf-8'],
        );
    }

    /**
     * Respuesta de redirección HTTP.
     */
    public static function redirect(string $url, int $statusCode = 302): self
    {
        return new self(
            statusCode: $statusCode,
            body: '',
            headers: ['Location' => $url],
        );
    }

    /**
     * Respuesta HTML cruda (fragment para AJAX modals, NO necesita layout).
     */
    public static function htmlFragment(string $body): self
    {
        return new self(
            statusCode: 200,
            body: $body,
            headers: ['Content-Type' => 'text/html; charset=utf-8', 'X-Fragment' => '1'],
        );
    }
}
