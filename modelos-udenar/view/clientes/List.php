<?php
// view/clientes/List.php
// Aquí recibimos las "props": $data (filas) y $columnsMeta (columnas)

$headers = array_column($columnsMeta, 'name'); // Extraemos solo los nombres de columnas

// Definimos el botón de "Nuevo" como una herramienta para la Card
$tools = '<button class="btn btn-brand btn-sm" data-toggle="modal" data-target="#modalNuevo">
            <i class="fas fa-plus mr-1"></i> Nuevo Cliente
          </button>';

// Renderizamos el componente Card que adentro tiene el componente Table
echo UI::Card(
    "<i class='fas fa-users mr-2'></i> Listado de Clientes",
    UI::Table($headers, $data, $tableName),
    $tools
);