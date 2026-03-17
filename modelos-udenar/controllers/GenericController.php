<?php
// controllers/GenericController.php

class GenericController
{
    protected $model;
    protected $tableName;

    public function __construct($mysqli, $tableName)
    {
        $this->tableName = $tableName;
        // Obtenemos el nombre real para las consultas SQL
        $realTable = TableRegistry::getRealTableName($tableName) ?? $tableName;
        $this->model = new GenericModel($mysqli, $realTable);
    }

    public function index()
    {
        return $this->model->getAll();
    }

    public function indexRelational($tableB, $fk, $display)
    {
        return $this->model->getAllRelational($tableB, $fk, $display);
    }

    public function store($rawData)
    {
        if (!empty($rawData['id'])) {
            return $this->update($rawData);
        }
        return $this->create($rawData);
    }

    private function create($rawData)
    {
        $rules = TableRegistry::getRules($this->tableName);
        $dto = new RequestDTO($rawData, $rules);

        if (!$dto->isValid()) {
            $this->sendResponse(422, ['success' => false, 'errors' => $dto->errors]);
        }

        $success = $this->model->save($dto->data);
        $this->sendResponse(200, [
            'success' => $success,
            'title' => '¡Registro Creado!',
            'message' => 'El nuevo registro ha sido añadido a la base de datos.',
        ]);
    }

    private function update($rawData)
    {
        $id = $rawData['id'];
        $rules = TableRegistry::getRules($this->tableName);
        $dto = new RequestDTO($rawData, $rules);

        if (!$dto->isValid()) {
            $this->sendResponse(422, ['success' => false, 'errors' => $dto->errors]);
        }

        $data = $dto->data;
        unset($data['id']); // Proteger ID

        $success = $this->model->save($data, $id);
        $this->sendResponse(200, [
            'success' => $success,
            'title' => 'Actualización Exitosa',
            'message' => 'Los cambios se han guardado correctamente.',
        ]);
    }

    protected function sendResponse($code, $data)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    public function getItem($id)
    {
        return $this->model->getById($id);
    }
    public function delete($id)
    {
        return $this->model->delete($id);
    }
    public function getExternalData($tableName, $displayColumn)
    {
        // Usamos el modelo para traer datos de otra tabla de forma limpia
        $db = $this->model->getConnection();
        $realTable = TableRegistry::getRealTableName($tableName) ?? $tableName;
        $res = $db->query(
            "SELECT id, $displayColumn FROM `$realTable` ORDER BY `$displayColumn` ASC",
        );
        return $res->fetch_all(MYSQLI_ASSOC);
    }
}
