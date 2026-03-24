<?php

declare(strict_types=1);

namespace App\Domain\Attribute;

/**
 * Atributo para definir relaciones de clave foránea entre entidades.
 *
 * **Capa:** Dominio (Anillo 1)
 *
 * Las relaciones son parte del modelo de dominio: "un pedido pertenece a
 * un cliente" es una regla de negocio, no de infraestructura.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Relation
{
    /**
     * @param string $references  Slug de la tabla referenciada
     * @param string $display     Columna a mostrar en selects/listados
     * @param string $placeholder Texto para la opción vacía del select
     */
    public function __construct(
        public readonly string $references,
        public readonly string $display,
        public readonly string $placeholder = 'Seleccione...',
    ) {
    }
}
