<?php
class RequestDTO {
    public $data = [];
    public $errors = [];

    public function __construct($rawData, $rules) {
        foreach ($rules as $field => $rule) {
            $value = trim($rawData[$field] ?? '');

            // 1. Validar Vacíos
            if (empty($value)) {
                $this->errors[$field] = "El campo $field es obligatorio.";
                continue;
            }

            // 2. Validar por Tipo y Regex
            switch ($rule['type']) {
                case 'email':
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) $this->errors[$field] = $rule['error'];
                    break;
                case 'text':
                case 'tel':
                    if (isset($rule['regex']) && !preg_match($rule['regex'], $value)) $this->errors[$field] = $rule['error'];
                    break;
                case 'int':
                    if (!is_numeric($value)) $this->errors[$field] = "Debe ser un número.";
                    break;
                case 'date':
                    if (!strtotime($value)) $this->errors[$field] = "Fecha inválida.";
                    break;
            }

            $this->data[$field] = $value;
        }
    }

    public function isValid() { return empty($this->errors); }
}