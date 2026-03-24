<?php

declare(strict_types=1);

namespace App\Presentation\Router;

use App\Application\DTO\Request;
use App\Application\DTO\Response;
use App\Application\Service\CrudService;
use App\Domain\Exception\HttpException;
use App\Infrastructure\Persistence\DatabaseInspector;
use App\Infrastructure\Persistence\GenericRepository;
use App\Infrastructure\Registry\ModelRegistry;
use App\Presentation\Controller\GenericController;
use App\Presentation\Middleware\CsrfMiddleware;

/**
 * Router: parsea la URL y despacha al controlador apropiado.
 *
 * **Capa:** Presentación (Anillo 4)
 *
 * **De 491 líneas a ~100.** Este Router SOLO hace 3 cosas:
 * 1. Parsea la URL en slug/action/id
 * 2. Resuelve el controlador vía ModelRegistry
 * 3. Despacha al método del controlador apropiado
 *
 * Ya NO:
 * - Descubre modelos (→ ModelRegistry)
 * - Extrae reglas de atributos (→ ModelRegistry)
 * - Renderiza vistas (→ GenericController)
 * - Crea GenericModel (→ GenericRepository vía Composition Root)
 * - Hace echo/header (→ Response value object)
 */
final class Router
{
    private string $slug;
    private string $action;
    private ?int $id;

    public function __construct(
        private readonly \PDO $pdo,
        private readonly ModelRegistry $registry,
        private readonly CsrfMiddleware $csrf,
    ) {
    }

    /**
     * Despacha la petición al controlador apropiado.
     *
     * @param Request $request Petición HTTP encapsulada
     * @return Response Respuesta HTTP
     */
    public function dispatch(Request $request): Response
    {
        $this->parseUrl($request->uri);

        // Dashboard — caso especial
        if ($this->slug === 'dashboard') {
            return $this->handleDashboard();
        }

        // Descubrir modelo por slug
        $modelClass = $this->registry->findBySlug($this->slug);
        if ($modelClass === null) {
            throw HttpException::notFound("Módulo no registrado: {$this->slug}");
        }

        // Extraer metadata
        $tableAttr = $this->registry->getTableAttribute($modelClass);
        $realTable = $tableAttr->name;
        $rules = $this->registry->extractRules($modelClass);

        // Crear dependencias (Composition Root local)
        $repository = new GenericRepository($this->pdo, $realTable);
        $service = new CrudService($repository, $rules);
        $inspector = new DatabaseInspector($this->pdo);

        // Resolver controlador específico o usar genérico
        $controller = $this->resolveController(
            $service, $inspector, $realTable, $rules,
        );

        // Despachar acción
        return match ($this->action) {
            'get'            => $controller->form($request, $this->id),
            'create', 'update' => $controller->store($request),
            'delete'         => $this->id !== null
                ? $controller->delete($this->id)
                : throw HttpException::notFound('ID requerido para eliminar.'),
            default          => $controller->index($request),
        };
    }

    /**
     * Maneja el dashboard (vista especial sin modelo).
     */
    private function handleDashboard(): Response
    {
        $inspector = new DatabaseInspector($this->pdo);
        $tablas = $inspector->getTables();

        extract(['tablas' => $tablas, 'basePath' => '/']);
        ob_start();
        include dirname(__DIR__, 2) . '/../views/dashboard.phtml';
        $content = (string) ob_get_clean();

        return Response::html($content);
    }

    /**
     * Parsea la URL en slug/action/id.
     */
    private function parseUrl(string $uri): void
    {
        $parts = explode('/', rtrim($uri, '/'));

        $this->slug = isset($parts[0]) && $parts[0] !== '' ? strtolower($parts[0]) : 'dashboard';
        $this->action = $parts[1] ?? 'list';
        $this->id = isset($parts[2]) && is_numeric($parts[2]) ? (int) $parts[2] : null;
    }

    /**
     * Resuelve controlador específico o crea uno genérico.
     */
    private function resolveController(
        CrudService $service,
        DatabaseInspector $inspector,
        string $tableName,
        array $rules,
    ): GenericController {
        $specific = 'App\\Presentation\\Controller\\' . ucfirst($this->slug) . 'Controller';

        if (class_exists($specific)) {
            return new $specific($service, $inspector, $this->registry, $this->csrf, $rules);
        }

        return new GenericController(
            service: $service,
            inspector: $inspector,
            registry: $this->registry,
            csrf: $this->csrf,
            tableName: $tableName,
            slug: $this->slug,
            rules: $rules,
        );
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getAllModuleSlugs(): array
    {
        return $this->registry->getAllSlugs();
    }
}
