<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Attribute\Column;
use App\Domain\Attribute\Relation;
use App\Domain\Attribute\Table;

/**
 * Entidad que representa un pedido (order).
 *
 * **Capa:** Dominio (Anillo 1)
 */
#[Table(name: 'orders', slug: 'orders')]
class Order
{
    #[Relation(
        references: 'clients',
        display: 'name',
        placeholder: 'Seleccione un cliente por favor',
    )]
    public int $id_client;

    #[Column(
        type: 'text',
        placeholder: 'Nombre del producto',
        minlength: 3,
    )]
    public string $product;

    #[Column(
        type: 'number',
        placeholder: 'Cantidad (1-999)',
        min: 1,
        max: 999,
    )]
    public int $stock;

    #[Column(
        type: 'date',
        placeholder: 'Fecha de entrega',
    )]
    public string $order_date;
}
