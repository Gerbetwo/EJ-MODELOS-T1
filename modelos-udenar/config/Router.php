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

        // Normalizamos: tabla siempre en minúsculas para rutas y archivos
        $this->tableName = isset($parts[0]) ? strtolower($parts[0]) : 'dashboard';
        $this->action    = $parts[1] ?? 'list';
        $this->id        = $parts[2] ?? null;
    }

    public function resolve()
    {
        if ($this->tableName === 'dashboard') {
            return $this->renderView('Dashboard', ['conn' => $this->conn]);
        }

        // --- EL CAMBIO PROFESIONAL ---
        // Buscamos el nombre real en el diccionario
        $realTableName = TableRegistry::getRealTableName($this->tableName);

        if (!$realTableName) {
            return "Error: Módulo no encontrado";
        }

        // El Controlador específico o genérico DEBE recibir el nombre REAL
        $controllerClass = ucfirst($this->tableName) . 'Controller';
        if (class_exists($controllerClass)) {
            $controller = new $controllerClass($this->conn);
        } else {
            $controller = new GenericController($this->conn, $realTableName);
        }

        $this->handleActions($controller);

        $inspector = new DatabaseInspector($this->conn);
        // USAR $realTableName AQUÍ PARA LOS METADATOS
        $columnsMeta = $inspector->getTableMetadata($realTableName);
        $data = $controller->index();

        return $this->renderView($this->tableName, [
            'data' => $data,
            'columnsMeta' => $columnsMeta,
            'tableName' => $this->tableName // Para la UI usamos el slug
        ]);
    }

    private function handleActions($controller)
    {
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

    private function renderView($viewName, $props = [])
    {
        // Extraemos las "props" para que sean variables simples en la vista
        extract($props);

        ob_start();
        $file = "view/{$viewName}/List.php";
        if (file_exists($file)) {
            include $file;
        } elseif (file_exists("view/{$viewName}.php")) {
            include "view/{$viewName}.php";
        } else {
            // Si no hay vista, usamos el componente UI automático
            $headers = array_column($columnsMeta, 'name');
            echo UI::Card(
                "Gestión de " . ucfirst($viewName),
                UI::Table($headers, $data, $viewName),
                '<button class="btn btn-brand btn-sm" data-toggle="modal" data-target="#modalForm"><i class="fas fa-plus"></i> Nuevo</button>'
            );
        }
        return ob_get_clean();
    }
}
