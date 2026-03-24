<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ValidationException;
use App\Model\GenericModel;
use App\Validation\RequestDTO;

/**
 * Controlador genérico que proporciona acciones CRUD para cualquier entidad.
 *
 * **¿Por qué Inyección de Dependencias (DI) en vez de crear objetos internamente?**
 *
 * En la versión anterior, el controlador creaba su propio GenericModel:
 * ```php
 * $this->model = new GenericModel($mysqli, $realTable); // Acoplamiento fuerte
 * ```
 *
 * Ahora recibimos PDO y las reglas como dependencias inyectadas:
 * ```php
 * public function __construct(private readonly \PDO $db, ...) // Acoplamiento débil
 * ```
 *
 * **Beneficios:**
 * 1. **Testabilidad:** En un unit test, puedes inyectar un mock de PDO.
 * 2. **Flexibilidad:** El Router decide qué PDO y qué reglas inyectar.
 * 3. **Single Source of Truth:** La conexión PDO se crea UNA vez en index.php
 *    y se pasa a todos los controladores. No hay riesgo de múltiples conexiones.
 *
 * **¿Por qué Constructor Property Promotion?**
 * PHP 8.0+ permite `private readonly \PDO $db` directamente en la firma del
 * constructor. Esto combina: declaración de propiedad + tipado + asignación
 * en una sola línea. Menos boilerplate, misma funcionalidad.
 */
class GenericController
{
    /** @var GenericModel Modelo para operaciones de base de datos */
    protected readonly GenericModel $model;

    /**
     * @param \PDO   $db        Conexión PDO inyectada
     * @param string $tableName Nombre real de la tabla en la BD
     * @param string $slug      Slug de la URL (para rutas y vistas)
     * @param array<string, array<string, mixed>> $rules Reglas de validación extraídas de Attributes
     */
    public function __construct(
        protected readonly \PDO $db,
        protected readonly string $tableName,
        protected readonly string $slug,
        protected readonly array $rules = [],
    ) {
        $this->model = new GenericModel($this->db, $this->tableName);
    }

    /**
     * Lista todos los registros.
     *
     * @return array<int, array<string, mixed>>
     */
    public function index(): array
    {
        return $this->model->getAll();
    }

    /**
     * Lista todos los registros con resolución de clave foránea (JOIN).
     *
     * @param string $tableB         Tabla referenciada
     * @param string $foreignKey     Columna FK
     * @param string $displayColumn  Columna a mostrar de la tabla referenciada
     * @return array<int, array<string, mixed>>
     */
    public function indexRelational(
        string $tableB,
        string $foreignKey,
        string $displayColumn,
    ): array {
        return $this->model->getAllRelational($tableB, $foreignKey, $displayColumn);
    }

    /**
     * Guarda o actualiza un registro según si contiene un ID.
     *
     * **¿Por qué lanzamos ValidationException en vez de enviar JSON directamente?**
     * Porque el controlador no debería decidir el formato de respuesta.
     * Esa responsabilidad es del Front Controller (index.php), que puede
     * responder con JSON (para AJAX) o redirigir a una vista de error.
     *
     * @param array<string, mixed> $rawData Datos crudos del request
     * @return array{success: bool, title: string, message: string}
     * @throws ValidationException Si los datos no pasan la validación
     */
    public function store(array $rawData): array
    {
        $id = !empty($rawData['id']) ? (int) $rawData['id'] : null;

        $dto = new RequestDTO($rawData, $this->rules);

        if (!$dto->isValid()) {
            throw new ValidationException($dto->errors);
        }

        $data = $dto->data;

        if ($id !== null) {
            unset($data['id']);
            $success = $this->model->save($data, $id);
            return [
                'success' => $success,
                'title'   => 'Actualización Exitosa',
                'message' => 'Los cambios se han guardado correctamente.',
            ];
        }

        $success = $this->model->save($data);
        return [
            'success' => $success,
            'title'   => '¡Registro Creado!',
            'message' => 'El nuevo registro ha sido añadido a la base de datos.',
        ];
    }

    /**
     * Obtiene un registro por ID.
     *
     * @param int $id ID del registro
     * @return ?array<string, mixed>
     */
    public function getItem(int $id): ?array
    {
        return $this->model->getById($id);
    }

    /**
     * Elimina un registro por ID.
     *
     * @param int $id ID del registro a eliminar
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->model->delete($id);
    }

    /**
     * Obtiene opciones para un select de relación (tabla externa).
     *
     * @param string $tableName     Nombre real de la tabla referenciada
     * @param string $displayColumn Columna a mostrar
     * @return array<int, array<string, mixed>>
     */
    public function getExternalData(string $tableName, string $displayColumn): array
    {
        return $this->model->getExternalOptions($tableName, $displayColumn);
    }
}
