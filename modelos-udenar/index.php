<?php
require_once 'config/Database.php';
require_once 'config/DatabaseInspector.php';
require_once 'models/GenericModel.php';
require_once 'includes/Components.php';

$tableName = $_GET['table'] ?? null;
$action = $_GET['action'] ?? 'list';
$inspector = new DatabaseInspector($conn);

if ($tableName) {
    $model = new GenericModel($conn, $tableName);
    $meta = $inspector->getTableMetadata($tableName);

    // Lógica de acciones (Delete/Save)
    if ($action === 'delete') {
        $model->delete($_GET['id']);
        header("Location: index.php?table=$tableName&msg=deleted");
        exit;
    }

    // Renderizado de Vista CRUD Genérica
    ob_start();
    $data = $model->getAll();
    $headers = array_column($meta, 'name');
    
    echo UI::Card(
        "Listado: " . ucfirst($tableName),
        UI::Table($headers, $data, $tableName),
        "fas fa-database"
    );
    $content = ob_get_clean();
} else {
    // Dashboard: Cards informativos dinámicos
    ob_start();
    $tables = $inspector->getTables();
    echo "<div class='row'>";
    foreach ($tables as $t) {
        $cardContent = "Registros: <span class='badge bg-brand'>{$t['count']}</span><br><br>";
        $cardContent .= "<a href='index.php?table={$t['name']}' class='btn btn-sm btn-brand btn-block'>Gestionar</a>";
        echo "<div class='col-md-3'>" . UI::Card(ucfirst($t['name']), $cardContent, "fas fa-table") . "</div>";
    }
    echo "</div>";
    $content = ob_get_clean();
}

include './modelos-udenar/Template.php';