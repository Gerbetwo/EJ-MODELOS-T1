<?php
// config/TableRegistry.php

class TableRegistry {
    private static $map = [
        'clientes' => [
            'table' => 'Clientes',
            'display' => 'nombre',
            'rules' => [
                'nombre' => ['type' => 'text', 'pattern' => '^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$', 'title' => 'Solo letras y espacios'],
                'correo' => ['type' => 'email', 'title' => 'Ingrese un correo electrónico válido'],
                'telefono' => ['type' => 'tel', 'pattern' => '^[0-9+]{7,15}$', 'title' => 'Mínimo 7 números']
            ]
        ],
        'pedidos' => [
            'table' => 'Pedidos',
            'display' => 'producto',
            'rules' => [
                'cliente_id' => ['type' => 'relation', 'references' => 'clientes'],
                'cantidad'   => ['type' => 'number', 'min' => 1, 'max' => 999],
                'producto'   => ['type' => 'text', 'minlength' => 3]
            ]
        ]
    ];

    public static function getRules($slug) {
        return self::$map[strtolower($slug)]['rules'] ?? [];
    }

    public static function getRealTableName($slug) {
        return self::$map[strtolower($slug)]['table'] ?? null;
    }

    public static function getAllModules() {
        return array_keys(self::$map);
    }
}