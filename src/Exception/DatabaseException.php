<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * Excepción para errores relacionados con la base de datos.
 *
 * **¿Por qué una excepción personalizada?**
 * Usar excepciones específicas nos permite capturar errores de BD de forma
 * granular en el Front Controller. Así podemos mostrar un error 500 amigable
 * al usuario sin exponer detalles internos de la base de datos (DSN, queries, etc.),
 * mientras logueamos el error real internamente.
 *
 * Extiende RuntimeException porque los errores de BD son errores en tiempo de
 * ejecución (no de lógica del programador), y no se pueden anticipar en compile-time.
 */
final class DatabaseException extends \RuntimeException
{
    /**
     * Crea una excepción de error de conexión.
     *
     * @param string     $message Mensaje descriptivo del error
     * @param \Throwable $previous Excepción original de PDO (para preservar el stack trace)
     */
    public static function connectionFailed(string $message, ?\Throwable $previous = null): self
    {
        return new self(
            message: "Error de conexión a la base de datos: {$message}",
            code: 0,
            previous: $previous,
        );
    }

    /**
     * Crea una excepción de error en consulta SQL.
     *
     * @param string     $message Mensaje descriptivo del error
     * @param \Throwable $previous Excepción original de PDO
     */
    public static function queryFailed(string $message, ?\Throwable $previous = null): self
    {
        return new self(
            message: "Error en consulta SQL: {$message}",
            code: 0,
            previous: $previous,
        );
    }
}
