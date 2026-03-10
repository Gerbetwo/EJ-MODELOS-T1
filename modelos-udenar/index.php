<?php
// index.php

// 1. Carga del Sistema (Config, Autoloader, .env)
require_once 'config/Config.php';

// 2. Inicialización de Servicios
$dbInstance = new Database();
$conn = $dbInstance->getConnection();
$inspector = new DatabaseInspector($conn);

// 3. Parámetros de Ruta (Querystring)
$tableName = $_GET['table'] ?? null;
$action    = $_GET['action'] ?? 'list';
$id        = $_GET['id'] ?? null;
$content   = "";

if ($tableName) {
    // Inicializamos el "Motor Genérico"
    $controller = new GenericController($conn, $tableName);
    $meta = $inspector->getTableMetadata($tableName);

    // --- CAPA DE ACCIONES (POST) ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($action === 'create') {
            $controller->store($_POST);
            header("Location: index.php?table=$tableName&msg=creado");
            exit;
        }
        if ($action === 'update' && isset($_POST['id'])) {
            $controller->update($_POST['id'], $_POST);
            header("Location: index.php?table=$tableName&msg=actualizado");
            exit;
        }
    }

    // --- CAPA DE ACCIONES (GET) ---
    if ($action === 'delete' && $id) {
        $controller->delete($id);
        header("Location: index.php?table=$tableName&msg=eliminado");
        exit;
    }

    // --- CAPA DE RENDERIZADO (VIEW) ---
    $data = $controller->index();
    $folder = strtolower($tableName);
    
    ob_start();
    // ¿Existe un "Page Component" específico para esta tabla?
    if (file_exists("view/$folder/List.php")) {
        include "view/$folder/List.php";
    } else {
        // Si no existe, usamos el renderizado automático de la librería UI
        $headers = array_column($meta, 'name');
        echo UI::Card(
            "<i class='fas fa-database mr-2'></i> Gestión de " . ucfirst($tableName),
            UI::Table($headers, $data, $tableName),
            '<button class="btn btn-brand btn-sm" data-toggle="modal" data-target="#modalForm"><i class="fas fa-plus"></i> Nuevo</button>'
        );
    }
    $content = ob_get_clean();

} else {
    // Vista por defecto: Dashboard
    ob_start();
    include 'view/Dashboard.php';
    $content = ob_get_clean();
}

// 4. Inyección en el Template Maestro
include 'includes/Template.php';