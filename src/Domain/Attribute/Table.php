<?php

declare(strict_types=1);

namespace App\Domain\Attribute;

/**
 * Atributo para marcar una clase como entidad de base de datos.
 *
 * **Capa:** Dominio (Anillo 1)
 *
 * Los Atributos viven en el Dominio porque definen la identidad y estructura
 * de las entidades — son metadata intrínseca al modelo de negocio. No dependen
 * de infraestructura (PDO, HTTP, etc.) ni de la capa de aplicación.
 *
 * En Doctrine ORM, los atributos #[Entity], #[Table] también viven en el
 * mismo namespace que las entidades.
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
