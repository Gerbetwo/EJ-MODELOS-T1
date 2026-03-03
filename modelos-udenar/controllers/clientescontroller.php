<?php
// controllers/clientescontroller.php
require_once __DIR__ . '/../models/clientes.php';

class ClientesController
{
    private $model;
    public function __construct($mysqli)
    {
        $this->model = new ClienteModel($mysqli);
    }

    // Devuelve arreglo de clientes
    public function index($buscar = '')
    {
        return $this->model->getAll($buscar);
    }

    public function get($id)
    {
        return $this->model->getById($id);
    }

    public function create($data)
    {
        // quitar campos vacíos indeseados
        $data = $this->sanitizeData($data);
        return $this->model->create($data);
    }

    public function update($id, $data)
    {
        $data = $this->sanitizeData($data);
        return $this->model->update($id, $data);
    }

    public function delete($id)
    {
        return $this->model->delete($id);
    }

    public function getColumns()
    {
        return $this->model->getColumns();
    }

    private function sanitizeData($data)
    {
        // quitar campos vacíos y limpiar valores simples
        $out = [];
        foreach ($data as $k => $v) {
            if ($k === 'id') {
                continue;
            }
            // opcional: puedes aplicar más sanitización aquí
            $out[$k] = trim($v);
        }
        return $out;
    }
}
