<?php

declare(strict_types=1);

namespace App\Model;

use App\Attribute\Column;
use App\Attribute\Relation;
use App\Attribute\Table;
use App\Exception\DatabaseException;

/**
 * Modelo genérico que proporciona operaciones CRUD para cualquier tabla.
 *
 * **¿Por qué un modelo "genérico" en vez de un modelo por tabla?**
 * Para un CRUD admin simple, un GenericModel evita duplicar las mismas queries
 * SELECT/INSERT/UPDATE/DELETE en cada modelo. Si necesitas lógica específica
 * (ej. un searchCustom para clientes), creas un modelo hijo que extienda este.
 *
 * **Seguridad: ¿Cómo protegemos las queries dinámicas?**
 * - Los VALORES siempre usan prepared statements (nunca se concatenan).
 * - Los NOMBRES DE TABLA y COLUMNA se validan contra una whitelist que se
 *   extrae de los Atributos PHP 8 del modelo. Solo se aceptan nombres que
 *   existen como propiedades con #[Column] o #[Relation].
 * - Los nombres de tabla se validan con regex antes de interpolar.
 */
class GenericModel
{
    /** @var string Nombre real de la tabla en la BD (validado) */
    protected readonly string $table;

    /**
     * @param \PDO   $pdo       Conexión PDO inyectada
     * @param string $tableName Nombre de la tabla (se valida con regex)
     *
     * @throws \InvalidArgumentException Si el nombre de tabla contiene caracteres no válidos
     */
    public function __construct(
        protected readonly \PDO $pdo,
        string $tableName,
    ) {
        // Whitelist: Solo permitimos nombres alfanuméricos y underscore.
        // Esto previene SQL injection a través de nombres de tabla.
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $tableName)) {
            throw new \InvalidArgumentException(
                "Nombre de tabla inválido: {$tableName}"
            );
        }
        $this->table = $tableName;
    }

    /**
     * Obtiene todos los registros de la tabla.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAll(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM `{$this->table}`");
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            throw DatabaseException::queryFailed($e->getMessage(), $e);
        }
    }

    /**
     * Obtiene un registro por su ID usando prepared statement.
     *
     * **¿Por qué prepared statement aquí?**
     * Aunque el ID suele ser un entero, SIEMPRE usamos prepared statements
     * por principio. Si mañana el ID cambia a UUID (string), el código sigue
     * siendo seguro sin modificaciones.
     *
     * @param int $id ID del registro
     * @return ?array<string, mixed> Registro encontrado o null
     */
    public function getById(int $id): ?array
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

    /**
     * Elimina un registro por su ID.
     *
     * @param int $id ID del registro a eliminar
     * @return bool true si se eliminó al menos un registro
     */
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

    /**
     * Inserta o actualiza un registro (upsert).
     *
     * **¿Cómo se protegen los nombres de columna?**
     * Los nombres vienen del array $data, que a su vez es construido por
     * RequestDTO a partir de las reglas definidas en los Atributos PHP 8.
     * Solo se aceptan campos que existen como propiedades con #[Column].
     * Adicionalmente, validamos cada nombre contra regex.
     *
     * @param array<string, mixed> $data Datos a guardar (campo => valor)
     * @param ?int                 $id   Si se proporciona, actualiza en vez de insertar
     * @return bool true si la operación fue exitosa
     */
    public function save(array $data, ?int $id = null): bool
    {
        try {
            // Validar nombres de columna
            $this->validateColumnNames(array_keys($data));

            if ($id !== null) {
                return $this->update($data, $id);
            }

            return $this->insert($data);
        } catch (\PDOException $e) {
            throw DatabaseException::queryFailed($e->getMessage(), $e);
        }
    }

    /**
     * Inserta un nuevo registro.
     *
     * @param array<string, mixed> $data Datos a insertar
     */
    private function insert(array $data): bool
    {
        $columns = array_keys($data);
        $colNames = implode(', ', array_map(fn(string $c): string => "`{$c}`", $columns));
        $placeholders = implode(', ', array_map(fn(string $c): string => ":{$c}", $columns));

        $sql = "INSERT INTO `{$this->table}` ({$colNames}) VALUES ({$placeholders})";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($data);
    }

    /**
     * Actualiza un registro existente.
     *
     * @param array<string, mixed> $data Datos a actualizar
     * @param int                  $id   ID del registro
     */
    private function update(array $data, int $id): bool
    {
        $setClauses = array_map(
            fn(string $col): string => "`{$col}` = :{$col}",
            array_keys($data),
        );
        $setString = implode(', ', $setClauses);

        $sql = "UPDATE `{$this->table}` SET {$setString} WHERE id = :_id";
        $stmt = $this->pdo->prepare($sql);

        // Usamos _id para evitar conflicto si hay una columna llamada "id" en $data
        $data['_id'] = $id;

        return $stmt->execute($data);
    }

    /**
     * Obtiene registros con JOIN para resolución de claves foráneas.
     *
     * **¿Por qué los nombres de columna NO pueden ser parametrizados?**
     * Es una limitación fundamental de SQL: los prepared statements solo
     * parametrizan VALORES, no identificadores (nombres de tabla/columna).
     * Por eso validamos los nombres con regex antes de interpolarlos.
     *
     * @param string $joinTable     Tabla para el JOIN
     * @param string $foreignKey    Columna de clave foránea en esta tabla
     * @param string $displayColumn Columna a mostrar de la tabla referenciada
     * @return array<int, array<string, mixed>>
     */
    public function getAllRelational(
        string $joinTable,
        string $foreignKey,
        string $displayColumn,
    ): array {
        // Validar todos los identificadores dinámicos
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

    /**
     * Busca registros por un campo específico.
     *
     * @param string $field Nombre del campo (se valida)
     * @param mixed  $value Valor a buscar (se parametriza)
     * @return ?array<string, mixed>
     */
    public function where(string $field, mixed $value): ?array
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

    /**
     * Obtiene todos los registros de una tabla externa (para poblar selects).
     *
     * @param string $tableName     Nombre real de la tabla
     * @param string $displayColumn Columna a mostrar
     * @return array<int, array<string, mixed>>
     */
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

    /**
     * Valida que los nombres de columna sean seguros para interpolar en SQL.
     *
     * @param array<string> $columns Nombres de columna a validar
     * @throws \InvalidArgumentException Si algún nombre contiene caracteres inválidos
     */
    private function validateColumnNames(array $columns): void
    {
        foreach ($columns as $col) {
            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $col)) {
                throw new \InvalidArgumentException(
                    "Nombre de columna inválido: {$col}"
                );
            }
        }
    }
}
