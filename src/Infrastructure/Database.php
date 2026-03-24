<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Exception\DatabaseException;

/**
 * Abstracción de conexión a base de datos usando PDO.
 *
 * **¿Por qué PDO en lugar de mysqli?**
 * 1. PDO soporta múltiples motores (MySQL, PostgreSQL, SQLite). Si mañana cambias
 *    de MariaDB a PostgreSQL, solo cambias el DSN.
 * 2. PDO tiene un sistema de prepared statements más consistente y seguro.
 * 3. PDO lanza excepciones nativas (PDOException) que encajan con nuestro sistema
 *    de excepciones personalizadas.
 * 4. PDO es el estándar de facto en el ecosistema PHP moderno (Symfony, Laravel, etc.).
 *
 * **¿Por qué Constructor Property Promotion?**
 * PHP 8.0+ permite declarar y asignar propiedades directamente en la firma del
 * constructor. Esto elimina el boilerplate de declarar la propiedad, tiparla,
 * y luego asignarla en el cuerpo del constructor. Menos código = menos bugs.
 *
 * **¿Por qué readonly?**
 * Las credenciales de BD no deben cambiar después de la construcción del objeto.
 * `readonly` (PHP 8.1+) lo garantiza a nivel de lenguaje.
 */
final class Database
{
    /** @var \PDO|null Conexión lazy (se crea solo cuando se necesita) */
    private ?\PDO $connection = null;

    /**
     * @param string $host     Host del servidor de base de datos
     * @param string $dbName   Nombre de la base de datos
     * @param string $user     Usuario de conexión
     * @param string $password Contraseña de conexión
     * @param int    $port     Puerto del servidor (default: 3306 para MySQL/MariaDB)
     */
    public function __construct(
        private readonly string $host,
        private readonly string $dbName,
        private readonly string $user,
        private readonly string $password,
        private readonly int $port = 3306,
    ) {
    }

    /**
     * Obtiene la conexión PDO (patrón Lazy Initialization).
     *
     * La conexión se crea la primera vez que se llama este método.
     * Las llamadas siguientes reutilizan la misma instancia.
     *
     * **¿Por qué Lazy Initialization y no crear la conexión en el constructor?**
     * Porque no todas las rutas necesitan acceso a la BD (ej. páginas estáticas).
     * Crear la conexión solo cuando se necesita ahorra recursos.
     *
     * @throws DatabaseException Si la conexión falla
     */
    public function getConnection(): \PDO
    {
        if ($this->connection === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                $this->host,
                $this->port,
                $this->dbName,
            );

            try {
                $this->connection = new \PDO($dsn, $this->user, $this->password, [
                    // ERRMODE_EXCEPTION: PDO lanza PDOException en cada error SQL.
                    // Sin esto, PDO retorna false silenciosamente (muy peligroso).
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,

                    // FETCH_ASSOC: Por defecto retorna arrays asociativos.
                    // Evita tener que especificarlo en cada fetch().
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,

                    // STRINGIFY_FETCHES false: Los tipos numéricos de MySQL
                    // se retornan como int/float en PHP, no como strings.
                    \PDO::ATTR_STRINGIFY_FETCHES => false,

                    // EMULATE_PREPARES false: Usa prepared statements REALES
                    // del motor de BD en vez de emularlos en PHP.
                    // Esto es más seguro contra SQL injection.
                    \PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (\PDOException $e) {
                // Envolvemos PDOException en nuestra excepción personalizada.
                // Así el Front Controller puede capturar DatabaseException
                // sin acoplarse a los detalles internos de PDO.
                throw DatabaseException::connectionFailed($e->getMessage(), $e);
            }
        }

        return $this->connection;
    }

    /**
     * Construye una instancia de Database a partir de variables de entorno.
     *
     * **Named Constructor (Static Factory Method):**
     * Este patrón permite crear instancias con nombres semánticos.
     * `Database::fromEnv()` es más expresivo que `new Database(...)` con 5 parámetros.
     */
    public static function fromEnv(): self
    {
        return new self(
            host: $_ENV['DB_HOST'] ?? 'localhost',
            dbName: $_ENV['DB_NAME'] ?? '',
            user: $_ENV['DB_USER'] ?? 'root',
            password: $_ENV['DB_PASS'] ?? '',
            port: (int) ($_ENV['DB_PORT'] ?? 3306),
        );
    }
}
