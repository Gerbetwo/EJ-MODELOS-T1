<?php
class GenericController {
    private $model;
    private $tableName;

    public function __construct($mysqli, $tableName) {
        $this->tableName = $tableName;
        $this->model = new GenericModel($mysqli, $tableName);
    }

    public function store($rawData) {
        $rules = TableRegistry::getRules($this->tableName);
        $dto = new RequestDTO($rawData, $rules);

        if (!$dto->isValid()) {
            $this->sendResponse(422, ['errors' => $dto->errors]);
        }

        // 1. EXTRAER ID (si existe) PARA DIFERENCIAR UPDATE DE CREATE
        $id = $rawData['id'] ?? null;
        $data = $dto->data;
        unset($data['id']); // Limpiamos el ID de los datos de inserción

        // 2. EVITAR DUPLICADOS (Ejemplo: por Correo o Nombre)
        // Buscamos si existe otro registro con el mismo dato único
        $uniqueField = $this->getUniqueField($rules);
        if ($uniqueField && isset($data[$uniqueField])) {
            $existing = $this->model->where($uniqueField, $data[$uniqueField]);
            // Si existe y no es el mismo ID que estamos editando -> ERROR
            if ($existing && $existing['id'] != $id) {
                $this->sendResponse(422, ['errors' => ["Ese $uniqueField ya está registrado en el sistema."]]);
            }
        }

        // 3. GUARDAR (Pasamos el ID para que el Model sepa si hacer UPDATE o INSERT)
        $success = $this->model->save($data, $id);

        if ($success) {
            $this->sendResponse(200, ['success' => true, 'message' => $id ? 'Actualizado correctamente' : 'Creado correctamente']);
        } else {
            $this->sendResponse(500, ['errors' => ['Error interno al procesar la base de datos']]);
        }
    }

    private function getUniqueField($rules) {
        foreach ($rules as $field => $rule) {
            if ($rule['unique'] ?? false) return $this->findOriginalKey($field);
        }
        return null;
    }

    private function findOriginalKey($lowerKey) {
        // Método auxiliar para mapear minúsculas a las llaves reales de la DB
        $inspector = new DatabaseInspector($this->model->getConnection());
        $meta = $inspector->getTableMetadata(TableRegistry::getRealTableName($this->tableName));
        foreach($meta as $m) { if(strtolower($m['name']) === $lowerKey) return $m['name']; }
        return $lowerKey;
    }

    private function sendResponse($code, $data) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}