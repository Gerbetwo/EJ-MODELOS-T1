<?php
// controllers/GenericController.php

class GenericController {
    protected $model;
    protected $tableName;

    public function __construct($mysqli, $tableName) {
        $this->tableName = $tableName;
        $this->model = new GenericModel($mysqli, $tableName);
    }

    public function index() { 
        return $this->model->getAll(); 
    }

    /**
     * Mapeo relacional para ver nombres en lugar de IDs (útil para Pedidos)
     */
    public function indexRelational($tableB, $fk, $display) {
        return $this->model->getAllRelational($tableB, $fk, $display);
    }

    /**
     * Punto de entrada único para guardado
     */
    public function store($rawData) {
        if (!empty($rawData['id'])) {
            return $this->update($rawData);
        }
        return $this->create($rawData);
    }

    private function create($rawData) {
        $rules = TableRegistry::getRules($this->tableName);
        $dto = new RequestDTO($rawData, $rules);

        if (!$dto->isValid()) {
            $this->sendResponse(422, ['success' => false, 'errors' => $dto->errors]);
        }

        $success = $this->model->save($dto->data);
        $this->sendResponse(200, [
            'success' => $success, 
            'message' => '¡Registro creado exitosamente!',
            'title' => 'Éxito'
        ]);
    }

    private function update($rawData) {
        $id = $rawData['id'];
        $rules = TableRegistry::getRules($this->tableName);
        $dto = new RequestDTO($rawData, $rules);

        if (!$dto->isValid()) {
            $this->sendResponse(422, ['success' => false, 'errors' => $dto->errors]);
        }

        $data = $dto->data;
        unset($data['id']); // Seguridad: no actualizar el ID

        $success = $this->model->save($data, $id);
        $this->sendResponse(200, [
            'success' => $success, 
            'message' => 'Los cambios se han guardado correctamente.',
            'title' => 'Actualización Exitosa'
        ]);
    }

    public function delete($id) { return $this->model->delete($id); }
    public function getItem($id) { return $this->model->getById($id); }

    protected function sendResponse($code, $data) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}