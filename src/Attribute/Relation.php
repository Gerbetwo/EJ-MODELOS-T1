<?php

declare(strict_types=1);

namespace App\Attribute;

/**
 * Atributo para definir relaciones de clave foránea entre entidades.
 *
 * **¿Por qué un atributo separado de Column?**
 * Las relaciones tienen una semántica completamente diferente a las columnas regulares:
 * necesitan saber a qué tabla referencian y qué columna mostrar en los selects.
 * Separarlos sigue el Single Responsibility Principle (SRP) y hace que el código
 * sea más legible:
 *
 * ```php
 * // Claro y semántico:
 * #[Relation(references: 'clients', display: 'name')]
 * public int $id_client;
 *
 * // vs. ambiguo con Column:
 * #[Column(type: 'relation', references: 'clients', display: 'name')]
 * public int $id_client;
 * ```
 *
 * En Doctrine ORM, esto sería equivalente a #[ManyToOne(targetEntity: Client::class)].
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Relation
{
    /**
     * @param string $references  Slug de la tabla referenciada (debe coincidir con #[Table(slug:)])
     * @param string $display     Columna de la tabla referenciada a mostrar en selects/listados
     * @param string $placeholder Texto para la opción vacía del select
     */
    public function __construct(
        public readonly string $references,
        public readonly string $display,
        public readonly string $placeholder = 'Seleccione...',
    ) {
    }
}
