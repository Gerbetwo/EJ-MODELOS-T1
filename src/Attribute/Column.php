<?php

declare(strict_types=1);

namespace App\Attribute;

/**
 * Atributo para definir las reglas de validación y display de una columna.
 *
 * Este atributo reemplaza las entradas individuales del array `rules` que antes
 * vivían en TableRegistry. Ahora cada propiedad del modelo lleva su propia
 * configuración de validación directamente encima.
 *
 * **¿Por qué IS_REPEATABLE no es necesario aquí?**
 * Cada propiedad del modelo representa una sola columna de la BD, así que
 * un solo #[Column] por propiedad es suficiente. Si en el futuro necesitas
 * múltiples reglas de validación por campo, podrías crear un atributo #[Validate]
 * separado y repeatable.
 *
 * @example
 * ```php
 * #[Column(type: 'email', placeholder: 'ejemplo@correo.com', error: 'Correo inválido.')]
 * public string $email;
 * ```
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Column
{
    /**
     * @param string  $type        Tipo de input HTML: 'text', 'email', 'number', 'date', 'tel'
     * @param string  $placeholder Texto placeholder del input
     * @param ?string $regex       Expresión regular para validación (ej. '/^[a-zA-Z]+$/')
     * @param ?string $error       Mensaje de error personalizado
     * @param ?int    $min         Valor mínimo (para inputs numéricos)
     * @param ?int    $max         Valor máximo (para inputs numéricos)
     * @param ?int    $minlength   Longitud mínima del texto
     * @param bool    $required    Si el campo es obligatorio (default: true)
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
