<?php

declare(strict_types=1);

namespace App\Model;

use App\Attribute\Column;
use App\Attribute\Table;

/**
 * Entidad que representa un cliente en la base de datos.
 *
 * **¿Por qué usamos Atributos PHP 8 (#[Table], #[Column]) aquí?**
 *
 * Antes, toda la metadata de esta entidad vivía en `TableRegistry.php`, un array
 * gigante centralizado. El problema es que si agregas un campo a la tabla `clients`,
 * tienes que recordar ir a TableRegistry a actualizar las reglas. Con Atributos,
 * la metadata vive JUNTO a la propiedad que describe — imposible olvidarlo.
 *
 * Esto se llama "Metadata Mapping Pattern" y es exactamente lo que hace
 * Doctrine ORM con sus atributos #[Entity], #[Column], etc.
 *
 * **¿Por qué las propiedades son públicas?**
 * En un modelo de datos simple (no un Value Object), las propiedades públicas
 * con tipado estricto son aceptables. Symfony Serializer y Doctrine Hydrator
 * trabajan mejor con propiedades públicas. Si quisieras encapsulación total,
 * usarías getters/setters, pero añadirían boilerplate sin beneficio real aquí.
 */
#[Table(name: 'clients', slug: 'clients')]
class Cliente
{
    #[Column(
        type: 'text',
        placeholder: 'Nombre Cliente',
        regex: '/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/',
        error: 'Solo letras.',
    )]
    public string $name;

    #[Column(
        type: 'email',
        placeholder: 'ejemplo@correo.com',
        error: 'Correo inválido.',
    )]
    public string $email;

    #[Column(
        type: 'tel',
        placeholder: '300 123 4567',
        regex: '/^[0-9+]{7,15}$/',
        error: 'Mínimo 7 números.',
    )]
    public string $telephone;

    #[Column(
        type: 'text',
        placeholder: 'Pasto',
        regex: '/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/',
        error: 'Solo letras.',
    )]
    public string $city;
}
