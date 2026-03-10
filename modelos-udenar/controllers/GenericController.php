<?php
// controllers/GenericController.php

class GenericController {
    private $model;
    private $tableName;

    public function __construct($mysqli, $tableName) {
        $this->tableName = $tableName;
        $this->model = new GenericModel($mysqli, $tableName);
    }

    public function index() {
        return $this->model->getAll();
    }

    public function store($data) {
        // Limpiamos el ID si viene en el POST para evitar errores de duplicado
        unset($data['id']); 
        return $this->model->save($data);
    }

    public function update($id, $data) {
        return $this->model->save($data, $id);
    }

    public function delete($id) {
        return $this->model->delete($id);
    }

    public function getItem($id) {
        return $this->model->getById($id);
    }
}