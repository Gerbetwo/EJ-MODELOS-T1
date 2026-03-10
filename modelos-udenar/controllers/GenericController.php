<?php
// controllers/GenericController.php

class GenericController {
    protected $model;
    protected $tableName;

    public function __construct($mysqli, $tableName) {
        $this->tableName = $tableName;
        $this->model = new GenericModel($mysqli, $tableName);
    }

    /**
     * MÉTODO FALTANTE: Retorna todos los registros para la tabla
     */
    public function index() {
        return $this->model->getAll();
    }

    /**
     * Procesa la creación y actualización con validación DTO
     */
    public function store($rawData) {
        $rules = TableRegistry::getRules($this->tableName);
        $dto = new RequestDTO($rawData, $rules);

        if (!$dto->isValid()) {
            $this->sendResponse(422, ['errors' => $dto->errors]);
        }

        $id = $rawData['id'] ?? null;
        $data = $dto->data;
        unset($data['id']); 

        // Guardar (Si hay ID hace UPDATE, si no hace INSERT)
        $success = $this->model->save($data, $id);

        if ($success) {
            $this->sendResponse(200, [
                'success' => true, 
                'message' => $id ? 'Registro actualizado' : 'Registro creado'
            ]);
        } else {
            $this->sendResponse(500, ['errors' => ['Error en la base de datos']]);
        }
    }

    public function delete($id) {
        return $this->model->delete($id);
    }

    public function getItem($id) {
        return $this->model->getById($id);
    }

    protected function sendResponse($code, $data) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}