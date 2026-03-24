<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Exception\DatabaseException;
use App\Domain\Repository\RepositoryInterface;

/**
 * Implementación concreta del RepositoryInterface usando PDO.
 *
 * **Capa:** Infraestructura (Anillo 3)
 *
 * **Renombrado:** De `GenericModel` a `GenericRepository`.
 *
 * **¿Por qué este cambio de nombre importa?**
 * "Model" en MVC se refiere a la lógica de negocio/dominio. Pero esta clase
 * hace queries SQL — es persistencia, no dominio. Llamarla "Repository"
 * aclara su verdadera responsabilidad.
 *
 * **Implementa `RepositoryInterface`** (del Dominio):
 * Esto es la Inversión de Dependencias en acción. La capa de Aplicación
 * depende de la interfaz (Dominio), y esta clase implementa esa interfaz.
 * Si mañana cambias a MongoDB, creas `MongoRepository` que implementa
 * la misma interfaz sin tocar nada en Application ni Domain.
 */
class GenericRepository implements RepositoryInterface
{
    protected readonly string $table;

    /**
     * @param \PDO   $pdo       Conexión PDO
     * @param string $tableName Nombre de tabla (validado con regex)
     */
    public function __construct(
        protected readonly \PDO $pdo,
        string $tableName,
    ) {
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $tableName)) {
            throw new \InvalidArgumentException("Nombre de tabla inválido: {$tableName}");
        }
        $this->table = $tableName;
    }

    /** @inheritDoc */
    public function findAll(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM `{$this->table}`");
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            throw DatabaseException::queryFailed($e->getMessage(), $e);
        }
    }

    /** @inheritDoc */
    public function findById(int $id): ?array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM `{$this->table}` WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch();
            return $result !== false ? $result : null;
        } catch (\PDOException $e) {
            throw DatabaseException::queryFailed($e->getMessage(), $e);
        }
    }

    /** @inheritDoc */
    public function save(array $data, ?int $id = null): bool
    {
        try {
            $this->validateColumnNames(array_keys($data));
            return $id !== null ? $this->update($data, $id) : $this->insert($data);
        } catch (\PDOException $e) {
            throw DatabaseException::queryFailed($e->getMessage(), $e);
        }
    }

    /** @inheritDoc */
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM `{$this->table}` WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            throw DatabaseException::queryFailed($e->getMessage(), $e);
        }
    }

    /** @inheritDoc */
    public function findAllWithRelation(
        string $joinTable,
        string $foreignKey,
        string $displayColumn,
    ): array {
        $this->validateColumnNames([$foreignKey, $displayColumn]);
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $joinTable)) {
            throw new \InvalidArgumentException("Nombre de tabla JOIN inválido: {$joinTable}");
        }

        try {
            $sql = "SELECT t1.*, t2.`{$displayColumn}` AS relation_name
                    FROM `{$this->table}` t1
                    LEFT JOIN `{$joinTable}` t2 ON t1.`{$foreignKey}` = t2.id";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            throw DatabaseException::queryFailed($e->getMessage(), $e);
        }
    }

    /** @inheritDoc */
    public function findOneBy(string $field, mixed $value): ?array
    {
        $this->validateColumnNames([$field]);

        try {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM `{$this->table}` WHERE `{$field}` = :value LIMIT 1"
            );
            $stmt->execute(['value' => $value]);
            $result = $stmt->fetch();
            return $result !== false ? $result : null;
        } catch (\PDOException $e) {
            throw DatabaseException::queryFailed($e->getMessage(), $e);
        }
    }

    /** @inheritDoc */
    public function getExternalOptions(string $tableName, string $displayColumn): array
    {
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $tableName)) {
            throw new \InvalidArgumentException("Nombre de tabla inválido: {$tableName}");
        }
        $this->validateColumnNames([$displayColumn]);

        try {
            $stmt = $this->pdo->query(
                "SELECT id, `{$displayColumn}` FROM `{$tableName}` ORDER BY `{$displayColumn}` ASC"
            );
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            throw DatabaseException::queryFailed($e->getMessage(), $e);
        }
    }

    // ─── Private helpers ────────────────────────────────────

    private function insert(array $data): bool
    {
        $columns = array_keys($data);
        $colNames = implode(', ', array_map(fn(string $c): string => "`{$c}`", $columns));
        $placeholders = implode(', ', array_map(fn(string $c): string => ":{$c}", $columns));

        $sql = "INSERT INTO `{$this->table}` ({$colNames}) VALUES ({$placeholders})";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    private function update(array $data, int $id): bool
    {
        $setClauses = array_map(
            fn(string $col): string => "`{$col}` = :{$col}",
            array_keys($data),
        );
        $sql = "UPDATE `{$this->table}` SET " . implode(', ', $setClauses) . " WHERE id = :_id";
        $stmt = $this->pdo->prepare($sql);
        $data['_id'] = $id;
        return $stmt->execute($data);
    }

    private function validateColumnNames(array $columns): void
    {
        foreach ($columns as $col) {
            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $col)) {
                throw new \InvalidArgumentException("Nombre de columna inválido: {$col}");
            }
        }
    }
}
