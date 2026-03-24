<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * Controlador específico para la entidad Clientes.
 *
 * **¿Por qué existe si hereda todo de GenericController?**
 * Este controlador existe por dos razones:
 *
 * 1. **Extensibilidad:** Si mañana necesitas una acción específica para clientes
 *    (ej. exportar a Excel, buscar por ciudad), la agregas aquí sin modificar
 *    el GenericController. Esto sigue el Open/Closed Principle (OCP de SOLID).
 *
 * 2. **Routing explícito:** El Router puede detectar que existe un controlador
 *    específico `ClientesController` y usarlo en vez del genérico, permitiendo
 *    personalización por módulo.
 *
 * En la práctica, muchos frameworks (Rails, Laravel) generan controladores
 * "vacíos" que heredan de un base controller. Es un scaffold que se llena
 * conforme el módulo crece.
 */
class ClientesController extends GenericController
{
    /**
     * @param \PDO                               $db    Conexión PDO inyectada
     * @param array<string, array<string, mixed>> $rules Reglas de validación
     */
    public function __construct(\PDO $db, array $rules = [])
    {
        parent::__construct(
            db: $db,
            tableName: 'clients',
            slug: 'clients',
            rules: $rules,
        );
    }
}
