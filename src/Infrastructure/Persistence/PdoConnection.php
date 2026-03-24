<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Exception\DatabaseException;
use App\Infrastructure\Config\EnvConfig;

/**
 * Conexión PDO con patrón Lazy Initialization.
 *
 * **Capa:** Infraestructura (Anillo 3)
 *
 * **Renombrado:** De `Database` a `PdoConnection` para ser más descriptivo.
 * "Database" es ambiguo — ¿es la conexión, el esquema, o el motor completo?
 * "PdoConnection" deja claro qué hace y qué tecnología usa.
 *
 * **Ya no accede a $_ENV directamente.** Recibe un `EnvConfig` inyectado.
 */
final class PdoConnection
{
    private ?\PDO $connection = null;

    /**
     * @param EnvConfig $config Configuración inyectada (no $_ENV directo)
     */
    public function __construct(
        private readonly EnvConfig $config,
    ) {
    }

    /**
     * Lazy-loaded PDO connection.
     *
     * @throws DatabaseException Si la conexión falla
     */
    public function get(): \PDO
    {
        if ($this->connection === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                $this->config->dbHost,
                $this->config->dbPort,
                $this->config->dbName,
            );

            try {
                $this->connection = new \PDO($dsn, $this->config->dbUser, $this->config->dbPassword, [
                    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE  => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_STRINGIFY_FETCHES    => false,
                    \PDO::ATTR_EMULATE_PREPARES     => false,
                ]);
            } catch (\PDOException $e) {
                throw DatabaseException::connectionFailed($e->getMessage(), $e);
            }
        }

        return $this->connection;
    }
}
