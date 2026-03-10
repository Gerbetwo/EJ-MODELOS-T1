<?php
// index.php - Punto de Entrada Único

// 1. Carga de Configuración y Autoload (Cerebro del sistema)
require_once 'config/Config.php'; 

// 2. Inicialización de Servicios
$dbInstance = new Database();
$conn = $dbInstance->getConnection();
$inspector = new DatabaseInspector($conn);

// 3. Captura de Parámetros (Routing)
$tableName = $_GET['table'] ?? null;
$action    = $_GET['action'] ?? 'list';
$content   = "";

// 4. Lógica de Enrutamiento Dinámico
if ($tableName) {
    // Verificamos si la tabla existe para evitar errores
    $tablesInDb = array_column($inspector->getTables(), 'name');
    
    if (in_array($tableName, $tablesInDb)) {
        $model = new GenericModel($conn, $tableName);
        $meta  = $inspector->getTableMetadata($tableName);
        $headers = array_column($meta, 'name');

        // Procesamiento de Acciones (Post/Delete)
        if ($action === 'delete' && isset($_GET['id'])) {
            $model->delete($_GET['id']);
            header("Location: index.php?table=$tableName&msg=eliminado");
            exit;
        }

        // Renderizado de la Vista (Componente List)
        $data = $model->getAll();
        ob_start();
        
        // Si tienes una vista específica (como clientes/List.php), úsala. 
        // Si no, usa una vista genérica.
        $specificView = "view/{$tableName}/List.php";
        if (file_exists($specificView)) {
            include $specificView;
        } else {
            // Renderizado genérico usando el componente UI
            echo UI::Card(
                "Gestión de " . ucfirst($tableName),
                UI::Table($headers, $data, $tableName)
            );
        }
        $content = ob_get_clean();
    } else {
        $content = UI::Card("Error", "La tabla solicitada no existe.", "fas fa-exclamation-triangle");
    }
} else {
    // Dashboard (Vista por defecto)
    ob_start();
    include 'view/Dashboard.php';
    $content = ob_get_clean();
}

// 5. Inyección en el Template Principal
include 'includes/Template.php';