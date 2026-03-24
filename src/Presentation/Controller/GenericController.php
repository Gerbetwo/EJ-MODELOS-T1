<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\DTO\Request;
use App\Application\DTO\Response;
use App\Application\Service\CrudService;
use App\Infrastructure\Persistence\DatabaseInspector;
use App\Infrastructure\Registry\ModelRegistry;
use App\Presentation\Middleware\CsrfMiddleware;

/**
 * Controlador genérico que traduce HTTP ↔ dominio.
 *
 * **Capa:** Presentación (Anillo 4)
 *
 * **¿Qué cambió respecto al controlador anterior?**
 *
 * ANTES (Fat Controller):
 * ```php
 * public function __construct(private PDO $db, ...) {
 *     $this->model = new GenericModel($db, $table); // Crea su propia dependencia
 * }
 * public function store(array $rawData) {
 *     $dto = new RequestDTO($rawData, $rules);       // Valida
 *     $this->model->save($dto->data);                 // Persiste
 *     return ['success' => true, ...];                 // Formatea respuesta
 * }
 * ```
 *
 * AHORA (Thin Controller — Single Responsibility):
 * ```php
 * public function __construct(private CrudService $service, ...) {}  // Recibe servicio
 * public function store(Request $request): Response {
 *     $result = $this->service->store($request->body);  // Delega al servicio
 *     return Response::json($result);                     // Solo traduce a HTTP
 * }
 * ```
 *
 * El controlador ya NO sabe qué es PDO, GenericModel, ni RequestDTO.
 * Solo conoce: Request → Service → Response.
 */
class GenericController
{
    public function __construct(
        protected readonly CrudService $service,
        protected readonly DatabaseInspector $inspector,
        protected readonly ModelRegistry $registry,
        protected readonly CsrfMiddleware $csrf,
        protected readonly string $tableName,
        protected readonly string $slug,
        protected readonly array $rules = [],
    ) {
    }

    /**
     * Maneja el listado de registros.
     */
    public function index(Request $request): Response
    {
        $columnsMeta = $this->inspector->getTableMetadata($this->tableName);

        // Detectar relación para JOIN automático
        $relationConfig = null;
        $foreignKey = null;
        foreach ($this->rules as $field => $rule) {
            if (($rule['type'] ?? '') === 'relation') {
                $relationConfig = $rule;
                $foreignKey = $field;
                break;
            }
        }

        if ($relationConfig !== null && $foreignKey !== null) {
            $refModelClass = $this->registry->findBySlug($relationConfig['references']);
            $referenceTable = $refModelClass !== null
                ? $this->registry->getTableAttribute($refModelClass)->name
                : $relationConfig['references'];

            $data = $this->service->listAllWithRelation(
                $referenceTable,
                $foreignKey,
                $relationConfig['display'],
            );
        } else {
            $data = $this->service->listAll();
        }

        // Extract column names for the table headers
        $headers = array_map(fn(array $col) => $col['name'], $columnsMeta);

        $content = $this->renderView('list', [
            'data'        => $data,
            'headers'     => $headers,
            'columnsMeta' => $columnsMeta,
            'tableName'   => $this->slug,
            'basePath'    => '/',
        ]);

        return Response::html($content);
    }

    /**
     * Maneja la solicitud AJAX del formulario (nuevo/editar).
     */
    public function form(Request $request, ?int $id): Response
    {
        $columnsMeta = $this->inspector->getTableMetadata($this->tableName);
        $rowData = $id !== null ? $this->service->getById($id) : null;
        $isEdit = $rowData !== null;
        $basePath = '/';
        $tableName = $this->slug;
        $rules = $this->rules;
        $controller = $this;
        $csrfToken = $this->csrf->getToken();

        $content = $this->renderView('form', compact(
            'columnsMeta', 'rowData', 'isEdit', 'basePath',
            'tableName', 'rules', 'controller', 'csrfToken',
        ));

        return Response::htmlFragment($content);
    }

    /**
     * Maneja POST para crear/actualizar.
     */
    public function store(Request $request): Response
    {
        if (!$request->isPost()) {
            return Response::json(['success' => false, 'message' => 'Método no permitido.'], 405);
        }

        // Validar CSRF
        $submittedToken = $request->body['_csrf_token'] ?? '';
        if (!$this->csrf->validateToken($submittedToken)) {
            return Response::json([
                'success' => false,
                'message' => 'Token CSRF inválido. Recarga la página e intenta de nuevo.',
            ], 403);
        }

        $result = $this->service->store($request->body);
        return Response::json($result);
    }

    /**
     * Maneja eliminación de registro.
     */
    public function delete(int $id): Response
    {
        $this->service->delete($id);
        return Response::redirect('/' . $this->slug . '?msg=eliminado');
    }

    /**
     * Obtiene opciones para selects (usado por las vistas de formulario).
     */
    public function getExternalData(string $tableName, string $displayColumn): array
    {
        return $this->service->getExternalOptions($tableName, $displayColumn);
    }

    /**
     * Renderiza una vista .phtml con variables inyectadas.
     */
    protected function renderView(string $template, array $props = []): string
    {
        extract($props);
        ob_start();

        $viewsDir = dirname(__DIR__, 2) . '/../views';
        $specificView = "{$viewsDir}/{$this->slug}/{$template}.phtml";
        $genericView = "{$viewsDir}/generic/{$template}.phtml";
        $fallbackView = "{$viewsDir}/clientes/{$template}.phtml";

        if (file_exists($specificView)) {
            include $specificView;
        } elseif (file_exists($genericView)) {
            include $genericView;
        } elseif (file_exists($fallbackView)) {
            include $fallbackView;
        }

        return (string) ob_get_clean();
    }
}
