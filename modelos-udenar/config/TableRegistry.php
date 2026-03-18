<?php
class TableRegistry
{
    private static $map = [
        'clients' => [
            'table' => 'clients',
            'rules' => [
                'name' => [
                    'type' => 'text',
                    'placeholder' => 'Nombre CLiente',
                    'regex' => '/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/',
                    'error' => 'Solo letras.',
                ],
                'email' => [
                    'type' => 'email',
                    'placeholder' => 'ejemplo@correo.com',
                    'error' => 'Correo inválido.',
                ],
                'telephone' => [
                    'type' => 'tel',
                    'placeholder' => '300 123 4567',
                    'regex' => '/^[0-9+]{7,15}$/',
                    'error' => 'Mínimo 7 números.',
                ],
                'city' => [
                    'type' => 'text',
                    'placeholder' => 'Pasto',
                    'regex' => '/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/',
                    'error' => 'Solo letras.',
                ],
            ],
        ],
        'orders' => [
            'table' => 'orders',
            'rules' => [
                'id_client' => [
                    'type' => 'relation',
                    'references' => 'clients',
                    'display' => 'name',
                    'placeholder' => 'Seleccione un cliente porfavor',
                ],
                'stock' => [
                    'type' => 'number',
                    'placeholder' => 'Cantidad (1-999)',
                    'min' => 1,
                    'max' => 999,
                ],
                'product' => [
                    'type' => 'text',
                    'placeholder' => 'Nombre del producto',
                    'minlength' => 3,
                ],
                'order_date' => ['type' => 'date', 'placeholder' => 'Fecha de entrega'],
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
