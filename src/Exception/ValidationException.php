<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * Excepción para errores de validación de datos de entrada.
 *
 * **¿Por qué no simplemente retornar un array de errores?**
 * Usar una excepción para validación permite "cortar" el flujo de ejecución
 * de forma limpia sin necesidad de `exit()` o `die()`. El Front Controller
 * la captura y decide si responder con JSON (para AJAX) o renderizar una vista
 * de error (para requests normales).
 *
 * Lleva un array asociativo de errores por campo, lo cual permite al frontend
 * mostrar mensajes de error junto a cada input del formulario.
 */
final class ValidationException extends \RuntimeException
{
    /**
     * @param array<string, string> $errors Mapa de campo => mensaje de error
     */
    public function __construct(
        private readonly array $errors,
        string $message = 'Los datos proporcionados no son válidos.',
        int $code = 422,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Obtiene el mapa de errores de validación.
     *
     * @return array<string, string> Campo => Mensaje de error
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
