<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Validation\RequestValidator;
use App\Domain\Exception\ValidationException;
use App\Domain\Repository\RepositoryInterface;

/**
 * Servicio de aplicación que orquesta los casos de uso CRUD.
 *
 * **Capa:** Aplicación (Anillo 2)
 *
 * **¿Qué es un Service Layer?**
 * Es la capa que coordina las operaciones de negocio. No contiene lógica de
 * negocio en sí (eso va en las Entidades), pero sí orquesta el flujo:
 * 1. Recibir datos crudos
 * 2. Validarlos
 * 3. Delegarlos al repositorio
 * 4. Retornar resultado
 *
 * **¿Por qué extraerlo del Controller?**
 * El Controller era un "Fat Controller" que validaba, persistía, y formateaba
 * la respuesta. Con el Service, el Controller solo traduce HTTP ↔ dominio.
 *
 * **Dependencias:**
 * - `RepositoryInterface` (abstracción del Dominio) — NO PDO ni GenericModel
 * - `RequestValidator` (Aplicación) — valida datos
 *
 * El diagrama de dependencias queda:
 * ```
 * Controller → CrudService → RepositoryInterface ← GenericRepository → PDO
 *                         → RequestValidator
 * ```
 * Nota cómo la flecha se INVIERTE en RepositoryInterface: las capas externas
 * dependen del centro. Esto es Dependency Inversion en acción.
 */
final class CrudService
{
    /**
     * @param RepositoryInterface $repository Repositorio inyectado (abstracción)
     * @param array<string, array<string, mixed>> $rules Reglas de validación
     */
    public function __construct(
        private readonly RepositoryInterface $repository,
        private readonly array $rules = [],
    ) {
    }

    /**
     * Lista todos los registros.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listAll(): array
    {
        return $this->repository->findAll();
    }

    /**
     * Lista todos los registros con resolución de clave foránea.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listAllWithRelation(
        string $joinTable,
        string $foreignKey,
        string $displayColumn,
    ): array {
        return $this->repository->findAllWithRelation($joinTable, $foreignKey, $displayColumn);
    }

    /**
     * Obtiene un registro por su ID.
     */
    public function getById(int $id): ?array
    {
        return $this->repository->findById($id);
    }

    /**
     * Crea o actualiza un registro con validación.
     *
     * @param array<string, mixed> $rawData Datos crudos del formulario
     * @return array{success: bool, title: string, message: string}
     * @throws ValidationException Si los datos no pasan validación
     */
    public function store(array $rawData): array
    {
        $id = !empty($rawData['id']) ? (int) $rawData['id'] : null;

        $validator = new RequestValidator($rawData, $this->rules);

        if (!$validator->isValid()) {
            throw new ValidationException($validator->errors);
        }

        $data = $validator->data;

        if ($id !== null) {
            unset($data['id']);
            $success = $this->repository->save($data, $id);
            return [
                'success' => $success,
                'title'   => 'Actualización Exitosa',
                'message' => 'Los cambios se han guardado correctamente.',
            ];
        }

        $success = $this->repository->save($data);
        return [
            'success' => $success,
            'title'   => '¡Registro Creado!',
            'message' => 'El nuevo registro ha sido añadido a la base de datos.',
        ];
    }

    /**
     * Elimina un registro por su ID.
     */
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Obtiene opciones para un select de relación.
     */
    public function getExternalOptions(string $tableName, string $displayColumn): array
    {
        return $this->repository->getExternalOptions($tableName, $displayColumn);
    }
}
