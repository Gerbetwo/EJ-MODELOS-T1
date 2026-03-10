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
        $this->tableName = isset($parts[0]) ? strtolower($parts[0]) : 'dashboard';
        $this->action = $parts[1] ?? 'list';
        $this->id = $parts[2] ?? null ?: null;
    }

    public function resolve()
    {
        if ($this->tableName === 'dashboard') {
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

        // AJAX: Formulario
        if ($this->action === 'get') {
            $this->renderAjaxForm($realTable, $controller);
            exit();
        }

        // POST: Guardar/Actualizar
        if (
            $_SERVER['REQUEST_METHOD'] === 'POST' &&
            ($this->action === 'create' || $this->action === 'update')
        ) {
            $controller->store($_POST);
            exit();
        }

        // DELETE
        if ($this->action === 'delete' && $this->id) {
            $controller->delete($this->id);
            header('Location: ' . BASE_URL . $this->tableName . '?msg=eliminado');
            exit();
        }

        // LISTADO
        $inspector = new DatabaseInspector($this->conn);
        $columnsMeta = $inspector->getTableMetadata($realTable);

        // Lógica especial para Pedidos (Cargar nombres de clientes en vez de IDs)
        if ($this->tableName === 'pedidos') {
            $data = $controller->indexRelational('Clientes', 'cliente_id', 'Nombre');
        } else {
            $data = $controller->index();
        }

        return $this->renderView($this->tableName, [
            'data' => $data,
            'columnsMeta' => $columnsMeta,
            'tableName' => $this->tableName,
        ]);
    }

    private function renderAjaxForm($realTable, $controller)
    {
        $inspector = new DatabaseInspector($this->conn);
        $columnsMeta = $inspector->getTableMetadata($realTable);
        $rowData = $this->id ? $controller->getItem($this->id) : null;
        $tableName = $this->tableName;
        $isEdit = isset($rowData);

        $formPath = "view/{$tableName}/Form.php";
        if (!file_exists($formPath)) {
            $formPath = 'view/clientes/Form.php';
        }
        include $formPath;
    }

    private function renderView($viewName, $props = [])
    {
        extract($props);
        ob_start();

        // 1. Intentar carpeta (List.php)
        // 2. Intentar archivo directo (.php)
        $file1 = "view/{$viewName}/List.php";
        $file2 = "view/{$viewName}.php";

        if (file_exists($file1)) {
            include $file1;
        } elseif (file_exists($file2)) {
            include $file2;
        } elseif (isset($columnsMeta)) {
            // Si no hay vista, generar tabla automática con estilo
            $headers = array_column($columnsMeta, 'name');
            echo UI::Card(
                'Gestión de ' . ucfirst($viewName),
                UI::Table($headers, $data, $viewName),
                $viewName,
            );
        } else {
            echo "<div class='alert alert-warning'>Vista no encontrada: $viewName</div>";
        }
        return ob_get_clean();
    }
}
