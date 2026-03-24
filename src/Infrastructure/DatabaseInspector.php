<?php

declare(strict_types=1);

namespace App\Infrastructure;

/**
 * Inspector de base de datos: lee metadatos de tablas y columnas.
 *
 * **¿Por qué seguimos usando queries dinámicas para nombres de tabla aquí?**
 * Los nombres de tabla/columna NO pueden ser parametrizados en prepared statements
 * (es una limitación de SQL, no de PDO). Por eso validamos que el nombre de tabla
 * exista realmente en la BD antes de usarlo (whitelist implícita), y usamos
 * backticks para escapar nombres reservados.
 *
 * En un ORM completo (Doctrine), esto se resuelve con un Schema Manager que
 * cachea los metadatos. Aquí mantenemos la simplicidad con queries directas
 * al INFORMATION_SCHEMA o SHOW TABLES.
 */
final class DatabaseInspector
{
    private readonly string $dbName;

    /**
     * @param \PDO $pdo Conexión PDO activa
     */
    public function __construct(
        private readonly \PDO $pdo,
    ) {
        // Obtenemos el nombre de la BD desde la conexión activa.
        $stmt = $this->pdo->query('SELECT DATABASE()');
        $this->dbName = (string) $stmt->fetchColumn();
    }

    /**
     * Obtiene la lista de todas las tablas con su conteo de registros.
     *
     * @return array<int, array{name: string, count: int}>
     */
    public function getTables(): array
    {
        $tables = [];
        $stmt = $this->pdo->query('SHOW TABLES');
        $tableNames = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tableNames as $tableName) {
            // Validamos que el nombre de tabla sea alfanumérico + underscore
            // para prevenir cualquier inyección a través de nombres de tabla maliciosos.
            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $tableName)) {
                continue;
            }

            $countStmt = $this->pdo->query(
                "SELECT COUNT(*) FROM `{$tableName}`"
            );
            $count = (int) $countStmt->fetchColumn();

            $tables[] = ['name' => $tableName, 'count' => $count];
        }

        return $tables;
    }

    /**
     * Obtiene los metadatos de las columnas de una tabla.
     *
     * @param string $tableName Nombre de la tabla
     * @return array<int, array{name: string, type: string, key: string, null: string, extra: string}>
     */
    public function getTableMetadata(string $tableName): array
    {
        // Validación de nombre de tabla (whitelist de caracteres seguros)
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $tableName)) {
            return [];
        }

        $columns = [];
        $stmt = $this->pdo->query("SHOW FULL COLUMNS FROM `{$tableName}`");

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $columns[] = [
                'name'  => $row['Field'],
                'type'  => $row['Type'],
                'key'   => $row['Key'],
                'null'  => $row['Null'],
                'extra' => $row['Extra'],
            ];
        }

        return $columns;
    }
}
