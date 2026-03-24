<?php

declare(strict_types=1);

namespace App\Domain\Exception;

/**
 * Excepción para errores HTTP (404, 403, 500, etc.).
 *
 * **Capa:** Dominio (Anillo 1)
 */
final class HttpException extends \RuntimeException
{
    public function __construct(
        private readonly int $statusCode,
        string $message = '',
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public static function notFound(string $message = 'Página no encontrada.'): self
    {
        return new self(statusCode: 404, message: $message);
    }

    public static function internalError(string $message = 'Error interno del servidor.'): self
    {
        return new self(statusCode: 500, message: $message);
    }
}
