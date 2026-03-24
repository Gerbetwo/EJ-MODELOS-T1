<?php

declare(strict_types=1);

namespace App\Infrastructure\Registry;

use App\Domain\Attribute\Column;
use App\Domain\Attribute\Relation;
use App\Domain\Attribute\Table;

/**
 * Registro de modelos: descubre entidades vía Reflection + Atributos PHP 8.
 *
 * **Capa:** Infraestructura (Anillo 3)
 *
 * **Extraído del Router** — antes las ~150 líneas de discoverModelClass(),
 * extractRulesFromModel(), getTableAttribute(), getAllModuleSlugs() vivían
 * dentro del Router (491 líneas). SRP exige que cada clase tenga UNA razón
 * de cambio. El Router cambia cuando cambian las rutas; el ModelRegistry
 * cambia cuando cambia la forma de descubrir modelos.
 *
 * **¿Por qué en Infraestructura y no en Dominio?**
 * Porque usa Reflection (API de PHP) y escaneo de filesystem (glob).
 * Eso es infraestructura. El Dominio define los Atributos; la Infraestructura
 * los lee y los descubre.
 */
final class ModelRegistry
{
    /** @var array<string, string> Cache: slug => FQCN */
    private array $modelCache = [];

    /** @var bool Si ya escaneamos todos los modelos */
    private bool $allScanned = false;

    public function __construct(
        private readonly string $modelDirectory,
        private readonly string $modelNamespace = 'App\\Domain\\Entity\\',
    ) {
    }

    /**
     * Busca la clase modelo que corresponde al slug dado.
     *
     * @return ?string FQCN de la clase, o null si no existe
     */
    public function findBySlug(string $slug): ?string
    {
        if (isset($this->modelCache[$slug])) {
            return $this->modelCache[$slug];
        }

        $this->scanAllModels();

        return $this->modelCache[$slug] ?? null;
    }

    /**
     * Obtiene el atributo #[Table] de una clase modelo.
     */
    public function getTableAttribute(string $className): Table
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
     * @return array<string, array<string, mixed>>
     */
    public function extractRules(string $className): array
    {
        $ref = new \ReflectionClass($className);
        $rules = [];

        foreach ($ref->getProperties() as $prop) {
            $propName = $prop->getName();

            // #[Column]
            $columnAttrs = $prop->getAttributes(Column::class);
            if (!empty($columnAttrs)) {
                /** @var Column $col */
                $col = $columnAttrs[0]->newInstance();
                $rule = ['type' => $col->type, 'placeholder' => $col->placeholder];

                if ($col->regex !== null)     $rule['regex'] = $col->regex;
                if ($col->error !== null)     $rule['error'] = $col->error;
                if ($col->min !== null)       $rule['min'] = $col->min;
                if ($col->max !== null)       $rule['max'] = $col->max;
                if ($col->minlength !== null) $rule['minlength'] = $col->minlength;
                $rule['required'] = $col->required;

                $rules[$propName] = $rule;
                continue;
            }

            // #[Relation]
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
     * Obtiene todos los slugs registrados (para el sidebar).
     *
     * @return array<string>
     */
    public function getAllSlugs(): array
    {
        $this->scanAllModels();

        return array_keys($this->modelCache);
    }

    /**
     * Escanea todos los archivos PHP en el directorio de modelos.
     */
    private function scanAllModels(): void
    {
        if ($this->allScanned) {
            return;
        }

        if (!is_dir($this->modelDirectory)) {
            $this->allScanned = true;
            return;
        }

        $files = glob($this->modelDirectory . '/*.php');
        if ($files === false) {
            $this->allScanned = true;
            return;
        }

        foreach ($files as $file) {
            $className = $this->modelNamespace . pathinfo($file, PATHINFO_FILENAME);

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
            $effectiveSlug = $tableAttr->slug ?: $tableAttr->name;
            $this->modelCache[$effectiveSlug] = $className;
        }

        $this->allScanned = true;
    }
}
