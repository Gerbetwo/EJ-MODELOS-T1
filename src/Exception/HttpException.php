<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * Excepción para errores HTTP (404, 403, 500, etc.).
 *
 * **¿Por qué una excepción para HTTP?**
 * En frameworks profesionales (Symfony, Laravel), los errores HTTP se modelan
 * como excepciones. Esto permite que cualquier parte del código lance un 404
 * (por ejemplo, "módulo no encontrado") sin necesidad de hacer `header() + exit()`.
 * El Front Controller la captura y renderiza la vista de error apropiada.
 */
final class HttpException extends \RuntimeException
{
    /**
     * @param int        $statusCode Código HTTP (404, 500, etc.)
     * @param string     $message    Mensaje descriptivo
     * @param \Throwable $previous   Excepción original si existe
     */
    public function __construct(
        private readonly int $statusCode,
        string $message = '',
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);
    }

    /**
     * Obtiene el código de estado HTTP.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Fábrica conveniente para errores 404.
     */
    public static function notFound(string $message = 'Página no encontrada.'): self
    {
        return new self(statusCode: 404, message: $message);
    }

    /**
     * Fábrica conveniente para errores 500.
     */
    public static function internalError(string $message = 'Error interno del servidor.'): self
    {
        return new self(statusCode: 500, message: $message);
    }
}
