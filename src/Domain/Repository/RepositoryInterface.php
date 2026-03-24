<?php

declare(strict_types=1);

namespace App\Domain\Repository;

/**
 * Contrato de persistencia para operaciones CRUD.
 *
 * **Capa:** Dominio (Anillo 1)
 *
 * **¿Por qué una interfaz en el Dominio?**
 * Esta es la pieza clave de la Onion Architecture y del principio de
 * Inversión de Dependencias (DIP — la "D" de SOLID):
 *
 * SIN interfaz (acoplamiento fuerte):
 * ```
 * Controller → GenericModel → PDO
 * (Presentación depende directamente de Infraestructura)
 * ```
 *
 * CON interfaz (inversión de dependencias):
 * ```
 * Controller → RepositoryInterface ← GenericRepository → PDO
 * (Presentación depende de abstracción del Dominio)
 * (Infraestructura implementa abstracción del Dominio)
 * ```
 *
 * **Beneficios concretos:**
 * 1. Puedes cambiar de MySQL a MongoDB sin tocar los controladores ni servicios.
 * 2. En tests, inyectas un InMemoryRepository que no necesita BD real.
 * 3. Las capas externas dependen del centro (Dominio), nunca al revés.
 *
 * En Symfony/Doctrine, esto equivale a `EntityRepository` que implementa
 * `ObjectRepository`. En Laravel, es el Repository Pattern sobre Eloquent.
 */
interface RepositoryInterface
{
    /**
     * Obtiene todos los registros.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findAll(): array;

    /**
     * Obtiene un registro por su ID.
     *
     * @param int $id ID del registro
     * @return ?array<string, mixed> null si no existe
     */
    public function findById(int $id): ?array;

    /**
     * Guarda (inserta o actualiza) un registro.
     *
     * @param array<string, mixed> $data Datos a guardar
     * @param ?int                 $id   Si se proporciona, actualiza; si no, inserta
     * @return bool true si la operación fue exitosa
     */
    public function save(array $data, ?int $id = null): bool;

    /**
     * Elimina un registro por su ID.
     *
     * @param int $id ID del registro a eliminar
     * @return bool true si se eliminó al menos un registro
     */
    public function delete(int $id): bool;

    /**
     * Obtiene todos los registros con resolución de clave foránea (JOIN).
     *
     * @param string $joinTable     Tabla referenciada
     * @param string $foreignKey    Columna FK
     * @param string $displayColumn Columna display de la tabla referenciada
     * @return array<int, array<string, mixed>>
     */
    public function findAllWithRelation(
        string $joinTable,
        string $foreignKey,
        string $displayColumn,
    ): array;

    /**
     * Busca un registro por un campo específico.
     *
     * @param string $field Nombre del campo
     * @param mixed  $value Valor a buscar
     * @return ?array<string, mixed>
     */
    public function findOneBy(string $field, mixed $value): ?array;

    /**
     * Obtiene opciones para selects de relación (tabla externa).
     *
     * @param string $tableName     Nombre de la tabla
     * @param string $displayColumn Columna a mostrar
     * @return array<int, array<string, mixed>>
     */
    public function getExternalOptions(string $tableName, string $displayColumn): array;
}
