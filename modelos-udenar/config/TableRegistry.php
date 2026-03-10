<?php
// config/TableRegistry.php

class TableRegistry {
    /**
     * Mapeo profesional: 'slug_url' => 'nombre_real_en_db'
     * Esto asegura que no importa cómo escriban la URL, 
     * PHP siempre usará el nombre correcto para la consulta SQL.
     */
    private static $map = [
        'clientes' => 'Clientes',
    ];

    public static function getRealTableName($slug) {
        $slug = strtolower($slug);
        return self::$map[$slug] ?? null;
    }

    public static function getAllModules() {
        return array_keys(self::$map);
    }
}