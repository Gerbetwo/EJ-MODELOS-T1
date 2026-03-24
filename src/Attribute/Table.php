<?php

declare(strict_types=1);

namespace App\Attribute;

/**
 * Atributo para marcar una clase como entidad de base de datos.
 *
 * **¿Por qué Atributos de PHP 8 en vez de un array centralizado (TableRegistry)?**
 *
 * 1. **Colocación (Co-location):** La metadata vive junto a la clase que describe.
 *    Si agregas una nueva entidad, solo creas una clase con #[Table] — no necesitas
 *    ir a otro archivo a registrarla. Esto sigue el principio de "alta cohesión".
 *
 * 2. **Refactorización segura:** Si renombras la clase, tu IDE puede encontrar
 *    todos los usos. Con un array centralizado, el IDE no puede rastrear las
 *    relaciones entre strings y clases.
 *
 * 3. **Estándar de la industria:** Doctrine ORM, Symfony Validator, y API Platform
 *    usan Atributos de PHP 8 para definir metadata. Es el patrón dominante.
 *
 * 4. **Reflection API:** PHP puede leer estos atributos en runtime usando
 *    ReflectionClass, lo cual es exactamente lo que hace nuestro Router para
 *    descubrir automáticamente qué modelo corresponde a cada slug de URL.
 *
 * @example
 * ```php
 * #[Table(name: 'clients', slug: 'clients')]
 * class Cliente {
 *     // ...
 * }
 * ```
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class Table
{
    /**
     * @param string $name Nombre real de la tabla en la base de datos
     * @param string $slug Slug usado en la URL (ej. "clients" → /clients/list)
     */
    public function __construct(
        public readonly string $name,
        public readonly string $slug = '',
    ) {
    }
}
