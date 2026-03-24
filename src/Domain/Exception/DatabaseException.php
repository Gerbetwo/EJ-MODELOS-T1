<?php

declare(strict_types=1);

namespace App\Domain\Exception;

/**
 * Excepción para errores de base de datos.
 *
 * **Capa:** Dominio (Anillo 1)
 *
 * **¿Por qué las excepciones están en el Dominio y no en Infraestructura?**
 * Porque representan contratos de error que la capa de Aplicación necesita
 * conocer. Si `DatabaseException` estuviera en Infraestructura, la capa de
 * Aplicación dependería de Infraestructura para capturarla — violando la
 * Regla de Dependencia de la Onion Architecture.
 *
 * La implementación concreta (PDO) lanza `PDOException`, pero la capa de
 * Infraestructura la envuelve en `DatabaseException` antes de propagarla.
 */
final class DatabaseException extends \RuntimeException
{
    public static function connectionFailed(string $message, ?\Throwable $previous = null): self
    {
        return new self(
            message: "Error de conexión a la base de datos: {$message}",
            code: 0,
            previous: $previous,
        );
    }

    public static function queryFailed(string $message, ?\Throwable $previous = null): self
    {
        return new self(
            message: "Error en consulta SQL: {$message}",
            code: 0,
            previous: $previous,
        );
    }
}
