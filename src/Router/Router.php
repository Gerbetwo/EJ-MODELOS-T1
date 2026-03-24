<?php

declare(strict_types=1);

namespace App\Router;

use App\Attribute\Column;
use App\Attribute\Relation;
use App\Attribute\Table;
use App\Controller\GenericController;
use App\Exception\HttpException;
use App\Exception\ValidationException;
use App\Infrastructure\DatabaseInspector;

/**
 * Router: resuelve la URL actual y despacha al controlador apropiado.
 *
 * **Cambios arquitectónicos respecto a la versión anterior:**
 *
 * 1. **Model Discovery con Atributos PHP 8:**
 *    En vez de consultar `TableRegistry::getRealTableName()`, el Router usa
 *    Reflection para escanear todas las clases en `src/Model/` y busca aquella
 *    cuyo `#[Table(slug:)]` coincida con el slug de la URL. Esto elimina
 *    completamente la clase TableRegistry.
 *
 * 2. **Sin exit() ni die():**
 *    El Router retorna un resultado (string HTML o array) en vez de llamar a
 *    `exit()`. El Front Controller decide qué hacer con el resultado.
 *
 * 3. **Inyección de Dependencias:**
 *    Recibe PDO vía constructor. No crea conexiones ni accede a globals.
 *
 * 4. **Separación de responsabilidades:**
 *    - `resolve()` → decide qué acción ejecutar
 *    - `renderView()` → solo renderiza vistas
 *    - `discoverModelClass()` → busca modelos por slug
 *    - `extractRulesFromModel()` → extrae reglas de Atributos PHP 8
 */
final class Router
{
    private string $slug;
    private string $action;
    private ?int $id;

    /** @var array<string, string> Cache de slug => FQCN del modelo */
    private array $modelCache = [];

    /**
     * Directorio base para buscar las vistas .phtml
     */
    private readonly string $viewsDir;

    /**
     * @param \PDO   $pdo      Conexión PDO inyectada
     * @param string $basePath Ruta base de la aplicación (para assets y links)
     */
    public function __construct(
        private readonly \PDO $pdo,
        private readonly string $basePath = '/',
    ) {
        $this->viewsDir = dirname(__DIR__, 2) . '/views';
        $this->parseUrl();
    }

    /**
     * Parsea la URL para extraer slug, acción e ID.
     *
     * URL format: /{slug}/{action}/{id}
     * Ejemplo: /clients/delete/5 → slug="clients", action="delete", id=5
     */
    private function parseUrl(): void
    {
        $url = $_GET['url'] ?? 'dashboard';
        $parts = explode('/', rtrim($url, '/'));

        $this->slug = isset($parts[0]) ? strtolower($parts[0]) : 'dashboard';
        $this->action = $parts[1] ?? 'list';
        $this->id = isset($parts[2]) && is_numeric($parts[2]) ? (int) $parts[2] : null;
    }

    /**
     * Resuelve la petición actual y retorna el contenido renderizado.
     *
     * @return string Contenido HTML renderizado
     * @throws HttpException Si el módulo no existe (404)
     */
    public function resolve(): string
    {
        // Dashboard
        if ($this->slug === 'dashboard') {
            return $this->renderView('dashboard', [
                'pdo'      => $this->pdo,
                'basePath' => $this->basePath,
            ]);
        }

        // Buscar modelo por slug usando Atributos PHP 8
        $modelClass = $this->discoverModelClass($this->slug);
        if ($modelClass === null) {
            throw HttpException::notFound("Módulo no registrado: {$this->slug}");
        }

        // Extraer metadata del modelo
        $tableAttr = $this->getTableAttribute($modelClass);
        $realTable = $tableAttr->name;
        $rules = $this->extractRulesFromModel($modelClass);

        // Crear controlador (con DI)
        $controllerClass = $this->resolveControllerClass($this->slug);
        $controller = $controllerClass !== null
            ? new $controllerClass($this->pdo, $rules)
            : new GenericController($this->pdo, $realTable, $this->slug, $rules);

        // Despachar acción
        return match ($this->action) {
            'get'    => $this->handleAjaxForm($realTable, $controller, $rules),
            'create', 'update' => $this->handleStore($controller),
            'delete' => $this->handleDelete($controller),
            default  => $this->handleList($controller, $realTable, $rules),
        };
    }

    /**
     * Maneja la solicitud AJAX del formulario (nuevo/editar).
     */
    private function handleAjaxForm(
        string $realTable,
        GenericController $controller,
        array $rules,
    ): string {
        $inspector = new DatabaseInspector($this->pdo);
        $columnsMeta = $inspector->getTableMetadata($realTable);
        $rowData = $this->id !== null ? $controller->getItem($this->id) : null;
        $tableName = $this->slug;
        $isEdit = $rowData !== null;
        $basePath = $this->basePath;

        ob_start();
        $formPath = "{$this->viewsDir}/{$tableName}/form.phtml";
        if (!file_exists($formPath)) {
            $formPath = "{$this->viewsDir}/clientes/form.phtml";
        }
        include $formPath;
        $content = ob_get_clean();

        // Enviar como respuesta HTML para el modal AJAX
        header('Content-Type: text/html; charset=utf-8');
        echo $content;
        return '';
    }

    /**
     * Maneja POST para crear/actualizar.
     *
     * @throws ValidationException Si los datos no pasan validación
     */
    private function handleStore(GenericController $controller): string
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw HttpException::notFound('Método no permitido.');
        }

        $result = $controller->store($_POST);

        // Responder como JSON para AJAX
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($result, JSON_THROW_ON_ERROR);
        return '';
    }

    /**
     * Maneja eliminación de registro.
     */
    private function handleDelete(GenericController $controller): string
    {
        if ($this->id === null) {
            throw HttpException::notFound('ID requerido para eliminar.');
        }

        $controller->delete($this->id);
        header('Location: ' . $this->basePath . $this->slug . '?msg=eliminado');
        return '';
    }

    /**
     * Maneja la vista de listado con detección dinámica de relaciones.
     */
    private function handleList(
        GenericController $controller,
        string $realTable,
        array $rules,
    ): string {
        $inspector = new DatabaseInspector($this->pdo);
        $columnsMeta = $inspector->getTableMetadata($realTable);

        // Detectar relaciones desde las reglas (extraídas de Atributos PHP 8)
        $relationConfig = null;
        $foreignKey = null;

        foreach ($rules as $field => $rule) {
            if (($rule['type'] ?? '') === 'relation') {
                $relationConfig = $rule;
                $foreignKey = $field;
                break;
            }
        }

        // Si hay una relación, usar JOIN para resolver nombres
        if ($relationConfig !== null && $foreignKey !== null) {
            $refModelClass = $this->discoverModelClass($relationConfig['references']);
            $referenceTable = $refModelClass !== null
                ? $this->getTableAttribute($refModelClass)->name
                : $relationConfig['references'];

            $data = $controller->indexRelational(
                $referenceTable,
                $foreignKey,
                $relationConfig['display'],
            );
        } else {
            $data = $controller->index();
        }

        $tableName = $this->slug;
        return $this->renderView($this->slug, [
            'data'        => $data,
            'columnsMeta' => $columnsMeta,
            'tableName'   => $tableName,
            'basePath'    => $this->basePath,
        ]);
    }

    /**
     * Renderiza una vista .phtml con las variables proporcionadas.
     *
     * **¿Por qué ob_start()/ob_get_clean()?**
     * Output buffering captura todo el HTML generado por `include` en un string,
     * en vez de enviarlo directamente al navegador. Esto nos permite:
     * 1. Insertar el contenido dentro del layout maestro (template.phtml)
     * 2. Manipular el HTML antes de enviarlo (ej. minificación)
     * 3. En caso de error, descartar el buffer sin output parcial
     *
     * @param string               $viewName Nombre de la vista (sin extensión)
     * @param array<string, mixed> $props    Variables disponibles en la vista
     */
    private function renderView(string $viewName, array $props = []): string
    {
        // extract() hace que cada key del array se convierta en una variable
        // local dentro de la vista. Ej: ['data' => [...]] → $data disponible.
        extract($props);
        ob_start();

        $file1 = "{$this->viewsDir}/{$viewName}/list.phtml";
        $file2 = "{$this->viewsDir}/{$viewName}.phtml";

        if (file_exists($file1)) {
            include $file1;
        } elseif (file_exists($file2)) {
            include $file2;
        } elseif (isset($columnsMeta, $data)) {
            // Fallback: tabla auto-generada (sin UI.php, directamente en la vista)
            $headers = array_column($columnsMeta, 'name');
            include "{$this->viewsDir}/generic/list.phtml";
        } else {
            throw HttpException::notFound("Vista no encontrada: {$viewName}");
        }

        return (string) ob_get_clean();
    }

    // ─────────────────────────────────────────────────────────────
    // Model Discovery via Reflection + PHP 8 Attributes
    // ─────────────────────────────────────────────────────────────

    /**
     * Descubre la clase modelo que corresponde al slug de la URL.
     *
     * **¿Cómo funciona el Model Discovery?**
     * 1. Escanea todos los archivos .php en src/Model/
     * 2. Para cada archivo, obtiene el FQCN (Fully Qualified Class Name)
     * 3. Usa Reflection para leer el atributo #[Table]
     * 4. Si el slug del #[Table] coincide con el slug de la URL, ¡lo encontró!
     *
     * **¿Por qué no un array/mapa manual?**
     * Porque con Atributos, agregar un nuevo modelo es tan simple como crear
     * una clase con #[Table(slug: 'nuevo-modulo')]. No necesitas tocar ningún
     * otro archivo. El Router lo descubre automáticamente.
     *
     * @param string $slug Slug de la URL
     * @return ?string FQCN de la clase modelo, o null si no se encuentra
     */
    private function discoverModelClass(string $slug): ?string
    {
        // Revisar cache primero
        if (isset($this->modelCache[$slug])) {
            return $this->modelCache[$slug];
        }

        $modelDir = dirname(__DIR__) . '/Model';
        if (!is_dir($modelDir)) {
            return null;
        }

        $files = glob($modelDir . '/*.php');
        if ($files === false) {
            return null;
        }

        foreach ($files as $file) {
            $className = 'App\\Model\\' . pathinfo($file, PATHINFO_FILENAME);

            if (!class_exists($className)) {
                continue;
            }

            $ref = new \ReflectionClass($className);
            $attrs = $ref->getAttributes(Table::class);

            if (empty($attrs)) {
                continue;
            }

            /** @var Table $tableAttr */
            $tableAttr = $attrs[0]->newInstance();

            // Cache tanto por slug como por nombre de tabla
            $effectiveSlug = $tableAttr->slug ?: $tableAttr->name;
            $this->modelCache[$effectiveSlug] = $className;

            if ($effectiveSlug === $slug) {
                return $className;
            }
        }

        return null;
    }

    /**
     * Obtiene el atributo #[Table] de una clase modelo.
     *
     * @param string $className FQCN de la clase
     */
    private function getTableAttribute(string $className): Table
    {
        $ref = new \ReflectionClass($className);
        $attrs = $ref->getAttributes(Table::class);

        if (empty($attrs)) {
            throw new \RuntimeException("La clase {$className} no tiene el atributo #[Table]");
        }

        return $attrs[0]->newInstance();
    }

    /**
     * Extrae reglas de validación de los Atributos PHP 8 de un modelo.
     *
     * **Este método es el reemplazo directo de TableRegistry::getRules()**
     *
     * Lee las propiedades de la clase modelo y, para cada una que tenga
     * #[Column] o #[Relation], construye un array de reglas compatible
     * con RequestDTO.
     *
     * @param string $className FQCN de la clase modelo
     * @return array<string, array<string, mixed>> Reglas por campo
     */
    private function extractRulesFromModel(string $className): array
    {
        $ref = new \ReflectionClass($className);
        $rules = [];

        foreach ($ref->getProperties() as $prop) {
            $propName = $prop->getName();

            // Buscar #[Column]
            $columnAttrs = $prop->getAttributes(Column::class);
            if (!empty($columnAttrs)) {
                /** @var Column $col */
                $col = $columnAttrs[0]->newInstance();
                $rule = ['type' => $col->type, 'placeholder' => $col->placeholder];

                if ($col->regex !== null) {
                    $rule['regex'] = $col->regex;
                }
                if ($col->error !== null) {
                    $rule['error'] = $col->error;
                }
                if ($col->min !== null) {
                    $rule['min'] = $col->min;
                }
                if ($col->max !== null) {
                    $rule['max'] = $col->max;
                }
                if ($col->minlength !== null) {
                    $rule['minlength'] = $col->minlength;
                }
                $rule['required'] = $col->required;

                $rules[$propName] = $rule;
                continue;
            }

            // Buscar #[Relation]
            $relationAttrs = $prop->getAttributes(Relation::class);
            if (!empty($relationAttrs)) {
                /** @var Relation $rel */
                $rel = $relationAttrs[0]->newInstance();
                $rules[$propName] = [
                    'type'        => 'relation',
                    'references'  => $rel->references,
                    'display'     => $rel->display,
                    'placeholder' => $rel->placeholder,
                ];
            }
        }

        return $rules;
    }

    /**
     * Resuelve la clase controlador específica para un slug.
     *
     * Convierte el slug a PascalCase + "Controller" y busca si existe.
     * Ej: "clients" → "App\Controller\ClientsController"
     *
     * @param string $slug Slug de la URL
     * @return ?string FQCN del controlador, o null si no existe
     */
    private function resolveControllerClass(string $slug): ?string
    {
        $className = 'App\\Controller\\' . ucfirst($slug) . 'Controller';
        return class_exists($className) ? $className : null;
    }

    /**
     * Obtiene el slug actual (para el template).
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Obtiene la ruta base (para assets y links).
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * Obtiene todos los slugs registrados (para el sidebar).
     *
     * Escanea todos los modelos y retorna sus slugs.
     *
     * @return array<string>
     */
    public function getAllModuleSlugs(): array
    {
        $modelDir = dirname(__DIR__) . '/Model';
        if (!is_dir($modelDir)) {
            return [];
        }

        $files = glob($modelDir . '/*.php');
        if ($files === false) {
            return [];
        }

        $slugs = [];
        foreach ($files as $file) {
            $className = 'App\\Model\\' . pathinfo($file, PATHINFO_FILENAME);
            if (!class_exists($className)) {
                continue;
            }

            $ref = new \ReflectionClass($className);
            $attrs = $ref->getAttributes(Table::class);
            if (empty($attrs)) {
                continue;
            }

            /** @var Table $tableAttr */
            $tableAttr = $attrs[0]->newInstance();
            $slugs[] = $tableAttr->slug ?: $tableAttr->name;
        }

        return $slugs;
    }
}
