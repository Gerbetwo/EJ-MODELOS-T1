<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Attribute\Column;
use App\Domain\Attribute\Table;

/**
 * Entidad que representa un cliente.
 *
 * **Capa:** Dominio (Anillo 1)
 *
 * **¿Qué es una Entidad en Onion Architecture?**
 * Una entidad es un objeto del dominio con identidad propia (tiene un ID).
 * A diferencia de un Value Object, dos entidades con los mismos datos pero
 * diferente ID son objetos DISTINTOS (Juan Pérez ID=1 ≠ Juan Pérez ID=2).
 *
 * La entidad SOLO define estructura y reglas de negocio (vía Atributos).
 * NO sabe nada de PDO, SQL, HTTP, formularios, ni vistas.
 * La persistencia es responsabilidad del Repository (capa de Infraestructura).
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
