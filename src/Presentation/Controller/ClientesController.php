<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Service\CrudService;
use App\Infrastructure\Persistence\DatabaseInspector;
use App\Infrastructure\Registry\ModelRegistry;
use App\Presentation\Middleware\CsrfMiddleware;

/**
 * Controlador específico para Clientes.
 *
 * **Capa:** Presentación (Anillo 4)
 */
class ClientesController extends GenericController
{
    public function __construct(
        CrudService $service,
        DatabaseInspector $inspector,
        ModelRegistry $registry,
        CsrfMiddleware $csrf,
        array $rules = [],
    ) {
        parent::__construct(
            service: $service,
            inspector: $inspector,
            registry: $registry,
            csrf: $csrf,
            tableName: 'clients',
            slug: 'clients',
            rules: $rules,
        );
    }
}
