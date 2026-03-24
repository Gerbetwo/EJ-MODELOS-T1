<?php

declare(strict_types=1);

namespace App\Domain\Exception;

/**
 * Excepción para errores de validación de datos de entrada.
 *
 * **Capa:** Dominio (Anillo 1)
 */
final class ValidationException extends \RuntimeException
{
    /**
     * @param array<string, string> $errors Mapa campo => mensaje de error
     */
    public function __construct(
        private readonly array $errors,
        string $message = 'Los datos proporcionados no son válidos.',
        int $code = 422,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /** @return array<string, string> */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
