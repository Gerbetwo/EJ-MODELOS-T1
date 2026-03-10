<?php
// config/Router.php

class Router
{
    private $conn;
    private $tableName;
    private $action;
    private $id;

    public function __construct($mysqli)
    {
        $this->conn = $mysqli;
        $this->parseUrl();
    }

    private function parseUrl()
    {
        $url = $_GET['url'] ?? 'dashboard';
        $parts = explode('/', rtrim($url, '/'));

        // Slug de la URL (ej: clientes)
        $this->tableName = isset($parts[0]) ? strtolower($parts[0]) : 'dashboard';
        $this->action    = $parts[1] ?? 'list';
        $this->id        = ($parts[2] ?? null) ?: null;
    }

    public function resolve()
    {
        if ($this->tableName === 'dashboard') {
            // AQUÍ PASAMOS LA VARIABLE $conn A LA VISTA
            return $this->renderView('Dashboard', ['conn' => $this->conn]);
        }

        // 1. Validar contra el registro central
        $realTable = TableRegistry::getRealTableName($this->tableName);
        if (!$realTable) {
            return "<div class='alert alert-danger'>Módulo no registrado.</div>";
        }

        // 2. Instanciar controlador
        $controllerClass = ucfirst($this->tableName) . 'Controller';
        $controller = class_exists($controllerClass)
            ? new $controllerClass($this->conn)
            : new GenericController($this->conn, $realTable);

        // 3. ACCIÓN AJAX: Retornar solo el pedazo del formulario
        if ($this->action === 'get') {
            $this->renderAjaxForm($realTable, $controller);
            exit; // Importante para que no cargue el Template.php
        }

        // 4. ACCIONES POST (Guardar/Actualizar)
        $this->handlePostActions($controller);

        // 5. VISTA NORMAL (Listado)
        $inspector = new DatabaseInspector($this->conn);
        $columnsMeta = $inspector->getTableMetadata($realTable);
        $data = $controller->index();

        return $this->renderView($this->tableName, [
            'data' => $data,
            'columnsMeta' => $columnsMeta,
            'tableName' => $this->tableName
        ]);
    }

    private function renderAjaxForm($realTable, $controller)
    {
        $inspector = new DatabaseInspector($this->conn);
        $columnsMeta = $inspector->getTableMetadata($realTable);

        // Si no hay ID en la URL, rowData será null (modo creación)
        $rowData = $this->id ? $controller->getItem($this->id) : null;
        $isEdit = isset($rowData); // Definimos isEdit para el formulario

        // Esta variable es la que usa el Form.php para el TableRegistry y el Action
        $tableName = $this->tableName;

        $formPath = "view/{$tableName}/Form.php";
        if (!file_exists($formPath)) $formPath = "view/clientes/Form.php";

        include $formPath;
    }

    private function handlePostActions($controller)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->action === 'create' || $this->action === 'update') {
                // El controlador procesará el DTO y responderá JSON
                $controller->store($_POST);
                exit;
            }
        }
        if ($this->action === 'delete' && $this->id) {
            $controller->delete($this->id);
            header("Location: " . BASE_URL . $this->tableName . "?msg=eliminado");
            exit;
        }
    }

    private function renderView($viewName, $props = [])
    {
        extract($props); // Esto convierte ['conn' => $db] en $conn
        ob_start();
        $file = "view/{$viewName}/List.php";

        if (file_exists($file)) {
            include $file;
        } elseif (isset($columnsMeta)) {
            // Generación automática de tabla si no existe List.php
            $headers = array_column($columnsMeta, 'name');
            echo UI::Card("Gestión de " . ucfirst($viewName), UI::Table($headers, $data, $viewName), $viewName);
        }
        return ob_get_clean();
    }
}
