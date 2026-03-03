<?php
// index.php - router principal
require_once 'config/connectdb.php';
require_once 'controllers/clientescontroller.php';

$module = $_GET['module'] ?? 'dashboard';
$controller = new ClientesController($conn);
$content = '';

if ($module === 'clientes') {
    $action = $_GET['action'] ?? 'index';

    // acciones que ejecutan y redirigen
    if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $ok = $controller->create($_POST);
        header('Location: index.php?module=clientes&msg=' . ($ok ? 'creado' : 'error'));
        exit;
    }

    if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id'] ?? 0);
        unset($_POST['id']);
        $ok = $controller->update($id, $_POST);
        header('Location: index.php?module=clientes&msg=' . ($ok ? 'actualizado' : 'error'));
        exit;
    }

    if ($action === 'delete' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $ok = $controller->delete($id);
        header('Location: index.php?module=clientes&msg=' . ($ok ? 'eliminado' : 'error'));
        exit;
    }

    // endpoint JSON para AJAX (obtener un cliente)
    if ($action === 'get' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $row = $controller->get($id);
        header('Content-Type: application/json');
        echo json_encode($row ?: []);
        exit;
    }

    // vista de listado (GET)
    $buscar = $_GET['buscar'] ?? '';
    $clientes = $controller->index($buscar);
    $columnsMeta = $controller->getColumns(); // para generar formularios dinámicos
    ob_start();
    include 'view/clientes/index.php';
    $content = ob_get_clean();
} else {
    // dashboard
    ob_start();
    include 'view/dashboard.php';
    $content = ob_get_clean();
}

// plantilla
include 'template.php';