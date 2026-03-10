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

    // --- LOGICA PARA CREAR ---
    public function create($rawData) {
        $rules = TableRegistry::getRules($this->tableName);
        $dto = new RequestDTO($rawData, $rules);

        if (!$dto->isValid()) {
            $this->sendResponse(422, ['errors' => $dto->errors]);
        }

        $success = $this->model->save($dto->data); // Sin ID = INSERT
        $this->sendResponse(200, ['success' => $success, 'message' => 'Registro creado con éxito']);
    }

    // --- LOGICA PARA ACTUALIZAR ---
    public function update($rawData) {
        $id = $rawData['id'] ?? null;
        if (!$id) $this->sendResponse(400, ['errors' => ['ID no proporcionado']]);

        $rules = TableRegistry::getRules($this->tableName);
        $dto = new RequestDTO($rawData, $rules);

        if (!$dto->isValid()) {
            $this->sendResponse(422, ['errors' => $dto->errors]);
        }

        $data = $dto->data;
        unset($data['id']); // Limpiamos para no intentar actualizar el ID mismo

        $success = $this->model->save($data, $id); // Con ID = UPDATE
        $this->sendResponse(200, ['success' => $success, 'message' => 'Registro actualizado correctamente']);
    }

    // El método store ahora solo actúa como un "Traffic Cop"
    public function store($rawData) {
        if (isset($rawData['id']) && !empty($rawData['id'])) {
            return $this->update($rawData);
        }
        return $this->create($rawData);
    }

    public function getItem($id) { return $this->model->getById($id); }
    public function delete($id) { return $this->model->delete($id); }

    protected function sendResponse($code, $data) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}