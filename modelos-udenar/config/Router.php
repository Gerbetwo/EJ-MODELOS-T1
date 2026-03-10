<?php
// config/Router.php

class Router {
    private $conn;
    private $tableName;
    private $action;
    private $id;

    public function __construct($mysqli) {
        $this->conn = $mysqli;
        $this->parseUrl();
    }

    private function parseUrl() {
        $url = $_GET['url'] ?? 'dashboard';
        $parts = explode('/', rtrim($url, '/'));
        $this->tableName = isset($parts[0]) ? strtolower($parts[0]) : 'dashboard';
        $this->action    = $parts[1] ?? 'list';
        $this->id        = ($parts[2] ?? null) ?: null; // Asegura null si está vacío
    }

    public function resolve() {
        if ($this->tableName === 'dashboard') {
            // Dashboard es un caso especial, no requiere metadatos
            return $this->renderView('Dashboard', ['conn' => $this->conn]);
        }

        $realTable = TableRegistry::getRealTableName($this->tableName);
        if (!$realTable) {
            return "<div class='alert alert-danger'>Módulo no registrado.</div>";
        }

        $controllerClass = ucfirst($this->tableName) . 'Controller';
        $controller = class_exists($controllerClass) 
            ? new $controllerClass($this->conn) 
            : new GenericController($this->conn, $realTable);

        // AJAX: Cargar Formulario
        if ($this->action === 'get') {
            $this->renderAjaxForm($realTable, $controller);
            exit;
        }

        $this->handlePostActions($controller);

        $inspector = new DatabaseInspector($this->conn);
        $columnsMeta = $inspector->getTableMetadata($realTable);
        $data = $controller->index();

        return $this->renderView($this->tableName, [
            'data' => $data,
            'columnsMeta' => $columnsMeta,
            'tableName' => $this->tableName
        ]);
    }

    private function renderAjaxForm($realTable, $controller) {
        $inspector = new DatabaseInspector($this->conn);
        $columnsMeta = $inspector->getTableMetadata($realTable);
        // Si no hay ID, $rowData será null (modo creación)
        $rowData = $this->id ? $controller->getItem($this->id) : null;
        $tableName = $this->tableName;
        
        $formPath = "view/{$tableName}/Form.php";
        if (!file_exists($formPath)) $formPath = "view/clientes/Form.php";
        
        include $formPath;
    }

    private function handlePostActions($controller) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->action === 'create') {
                $controller->store($_POST);
                header("Location: " . BASE_URL . $this->tableName . "?msg=creado");
                exit;
            }
            if ($this->action === 'update') {
                $controller->update($_POST['id'], $_POST);
                header("Location: " . BASE_URL . $this->tableName . "?msg=actualizado");
                exit;
            }
        }
        if ($this->action === 'delete' && $this->id) {
            $controller->delete($this->id);
            header("Location: " . BASE_URL . $this->tableName . "?msg=eliminado");
            exit;
        }
    }

    private function renderView($viewName, $props = []) {
        extract($props);
        ob_start();
        
        // Lógica de búsqueda de archivos más robusta
        $paths = [
            "view/{$viewName}/List.php",
            "view/{$viewName}.php"
        ];

        $found = false;
        foreach ($paths as $path) {
            if (file_exists($path)) {
                include $path;
                $found = true;
                break;
            }
        }

        if (!$found) {
            // Renderizado automático solo si hay metadatos
            if (isset($columnsMeta)) {
                $headers = array_column($columnsMeta, 'name');
                $tools = '<button class="btn btn-brand btn-sm btn-new-js" data-table="'.$tableName.'"><i class="fas fa-plus"></i> Nuevo</button>';
                echo UI::Card("Gestión de ".ucfirst($tableName), UI::Table($headers, $data, $tableName), $tableName);
            } else {
                echo "<div class='alert alert-warning'>Vista no encontrada para: $viewName</div>";
            }
        }
        return ob_get_clean();
    }
}