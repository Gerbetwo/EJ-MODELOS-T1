<?php
// controllers/GenericController.php

class GenericController
{
    private $model;
    private $tableName;

    public function __construct($mysqli, $tableName)
    {
        $this->tableName = $tableName;
        $this->model = new GenericModel($mysqli, $tableName);
    }

    public function index()
    {
        return $this->model->getAll();
    }

    // En controllers/GenericController.php

    public function store($data)
    {
        $this->secureValidate($data);
        return $this->model->save($data);
    }

    private function secureValidate($data)
    {
        foreach ($data as $key => $value) {
            // Validar Correo
            if (strpos($key, 'correo') !== false || strpos($key, 'email') !== false) {
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    die("Error: El formato de correo es inválido.");
                }
            }
            // Validar Números
            if ($key === 'cantidad' && (!is_numeric($value) || $value < 1)) {
                die("Error: La cantidad debe ser un número positivo.");
            }
        }
    }

    public function update($id, $data)
    {
        return $this->model->save($data, $id);
    }

    public function delete($id)
    {
        return $this->model->delete($id);
    }

    public function getItem($id)
    {
        return $this->model->getById($id);
    }
}
