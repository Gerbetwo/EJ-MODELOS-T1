<?php
class TableRegistry
{
    private static $map = [
        'clientes' => [
            'table' => 'Clientes',
            'rules' => [
                'nombre'   => ['type' => 'text', 'placeholder' => 'Nombre completo', 'regex' => '^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$', 'error' => 'Solo letras.'],
                'correo'   => ['type' => 'email', 'placeholder' => 'ejemplo@correo.com', 'error' => 'Correo inválido.'],
                'telefono' => ['type' => 'tel', 'placeholder' => '300 123 4567', 'regex' => '^[0-9+]{7,15}$', 'error' => 'Mínimo 7 números.']
            ]
        ],
        'pedidos' => [
            'table' => 'Pedidos',
            'rules' => [
                'cliente_id' => ['type' => 'relation', 'references' => 'clientes', 'display' => 'nombre', 'placeholder' => 'Seleccione un cliente'],
                'cantidad'   => ['type' => 'number', 'placeholder' => 'Cantidad (1-999)', 'min' => 1, 'max' => 999],
                'producto'   => ['type' => 'text', 'placeholder' => 'Nombre del producto', 'minlength' => 3],
                'fecha_pedido' => ['type' => 'date', 'placeholder' => 'Fecha de entrega']
            ]
        ]
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
