<?php

declare(strict_types=1);

namespace App\Domain\Attribute;

/**
 * Atributo para definir reglas de validación y display de una columna.
 *
 * **Capa:** Dominio (Anillo 1)
 *
 * Las reglas de validación son parte del dominio porque definen las
 * invariantes del negocio (ej. "un email debe tener formato válido",
 * "el stock mínimo es 1"). No son reglas de infraestructura.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Column
{
    /**
     * @param string  $type        Tipo de input: 'text', 'email', 'number', 'date', 'tel'
     * @param string  $placeholder Texto placeholder del input
     * @param ?string $regex       Expresión regular de validación
     * @param ?string $error       Mensaje de error personalizado
     * @param ?int    $min         Valor mínimo (numérico)
     * @param ?int    $max         Valor máximo (numérico)
     * @param ?int    $minlength   Longitud mínima de texto
     * @param bool    $required    Si el campo es obligatorio
     */
    public function __construct(
        public readonly string $type = 'text',
        public readonly string $placeholder = '',
        public readonly ?string $regex = null,
        public readonly ?string $error = null,
        public readonly ?int $min = null,
        public readonly ?int $max = null,
        public readonly ?int $minlength = null,
        public readonly bool $required = true,
    ) {
    }
}
