<?php
class RequestDTO {
    public $data = [];
    public $errors = [];

    public function __construct($rawData, $rules) {
        foreach ($rules as $field => $rule) {
            $value = trim($rawData[$field] ?? '');

            if (empty($value)) {
                $this->errors[$field] = "El campo $field es obligatorio.";
                continue;
            }

            switch ($rule['type']) {
                case 'email':
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) $this->errors[$field] = $rule['error'];
                    break;
                case 'text':
                case 'tel':
                    // Validación segura de Regex
                    if (isset($rule['regex']) && !@preg_match($rule['regex'], $value)) {
                        $this->errors[$field] = $rule['error'];
                    }
                    break;
                case 'number':
                case 'int':
                    if (!is_numeric($value)) $this->errors[$field] = "Debe ser un número.";
                    break;
            }
            $this->data[$field] = $value;
        }
    }
    public function isValid() { return empty($this->errors); }
}