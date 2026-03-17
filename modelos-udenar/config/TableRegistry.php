<?php
class TableRegistry
{
    private static $map = [
        'Clientes' => [
            'table' => 'Clientes',
            'rules' => [
                'nombre' => [
                    'type' => 'text',
                    'placeholder' => 'Nombre completo',
                    'regex' => '/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/',
                    'error' => 'Solo letras.',
                ],
                'email' => [
                    'type' => 'email',
                    'placeholder' => 'ejemplo@correo.com',
                    'error' => 'Correo inválido.',
                ],
                'telefono' => [
                    'type' => 'tel',
                    'placeholder' => '300 123 4567',
                    'regex' => '/^[0-9+]{7,15}$/',
                    'error' => 'Mínimo 7 números.',
                ],
                'ciudad' => [
                    'type' => 'text',
                    'placeholder' => 'Pasto',
                    'regex' => '/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/',
                    'error' => 'Solo letras.',
                ],
            ],
        ],
        'Pedidos' => [
            'table' => 'Pedidos',
            'rules' => [
                'id_cliente' => [
                    'type' => 'relation',
                    'references' => 'clientes',
                    'display' => 'Nombre',
                    'placeholder' => 'Seleccione un cliente',
                ],
                'cantidad' => [
                    'type' => 'number',
                    'placeholder' => 'Cantidad (1-999)',
                    'min' => 1,
                    'max' => 999,
                ],
                'producto' => [
                    'type' => 'text',
                    'placeholder' => 'Nombre del producto',
                    'minlength' => 3,
                ],
                'fecha_pedido' => ['type' => 'date', 'placeholder' => 'Fecha de entrega'],
            ],
        ],
    ];

    public static function getAllModules()
    {
        return array_keys(self::$map);
    }
    public static function getRules($slug)
    {
        return self::$map[strtolower($slug)]['rules'] ?? [];
    }
    public static function getRealTableName($slug)
    {
        return self::$map[strtolower($slug)]['table'] ?? null;
    }
}
