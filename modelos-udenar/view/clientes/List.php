<?php
// view/clientes/List.php
// Aquí recibimos las props: $data, $columnsMeta, $tableName

$headers = array_column($columnsMeta, 'name');

// NO definas $tools manualmente con HTML. 
// Simplemente pasa el $tableName a UI::Card y él hará el resto.
echo UI::Card(
    "<i class='fas fa-users mr-2'></i> Listado de " . ucfirst($tableName),
    UI::Table($headers, $data, $tableName),
    $tableName // Pasamos el slug 'clientes' aquí
);