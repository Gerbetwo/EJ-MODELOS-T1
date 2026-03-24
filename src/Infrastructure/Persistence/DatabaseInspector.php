<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

/**
 * Inspector de base de datos: lee metadatos de tablas y columnas.
 *
 * **Capa:** Infraestructura (Anillo 3)
 *
 * Movido desde Infrastructure/DatabaseInspector.php a Persistence/ para
 * agrupar todo lo relacionado con persistencia de datos en un solo lugar.
 */
final class DatabaseInspector
{
    private readonly string $dbName;

    public function __construct(
        private readonly \PDO $pdo,
    ) {
        $stmt = $this->pdo->query('SELECT DATABASE()');
        $this->dbName = (string) $stmt->fetchColumn();
    }

    /** @return array<int, array{name: string, count: int}> */
    public function getTables(): array
    {
        $tables = [];
        $stmt = $this->pdo->query('SHOW TABLES');
        $tableNames = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tableNames as $tableName) {
            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $tableName)) {
                continue;
            }
            $countStmt = $this->pdo->query("SELECT COUNT(*) FROM `{$tableName}`");
            $count = (int) $countStmt->fetchColumn();
            $tables[] = ['name' => $tableName, 'count' => $count];
        }

        return $tables;
    }

    /** @return array<int, array{name: string, type: string, key: string, null: string, extra: string}> */
    public function getTableMetadata(string $tableName): array
    {
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
