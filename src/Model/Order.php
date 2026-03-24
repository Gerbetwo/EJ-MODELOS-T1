<?php

declare(strict_types=1);

namespace App\Model;

use App\Attribute\Column;
use App\Attribute\Relation;
use App\Attribute\Table;

/**
 * Entidad que representa un pedido (order) en la base de datos.
 *
 * **Ejemplo de Relación con Atributos:**
 * La propiedad `$id_client` usa #[Relation] en vez de #[Column] para indicar
 * que es una clave foránea. El Router y los formularios usan esta información
 * para generar selects dinámicos y JOINs automáticos.
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
