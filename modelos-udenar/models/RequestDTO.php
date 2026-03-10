<?php
class RequestDTO {
    public $data = [];
    public $errors = [];

    public function __construct($rawData, $rules) {
        // Normalizamos las llaves del POST para que coincidan con las reglas
        $normalizedInput = array_change_key_case($rawData, CASE_LOWER);

        foreach ($rules as $field => $rule) {
            $fieldLower = strtolower($field);
            $value = isset($normalizedInput[$fieldLower]) ? trim($normalizedInput[$fieldLower]) : null;

            // 1. Validación de Obligatoriedad
            if (($rule['required'] ?? true) && ($value === null || $value === '')) {
                $this->errors[$field] = "El campo " . ucfirst($field) . " es obligatorio.";
                continue;
            }

            if ($value !== null && $value !== '') {
                // 2. Validación por Tipo
                switch ($rule['type']) {
                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) 
                            $this->errors[$field] = $rule['error'] ?? "Formato de correo inválido.";
                        break;
                    
                    case 'date':
                        $d = DateTime::createFromFormat('Y-m-d', $value);
                        if (!($d && $d->format('Y-m-d') === $value))
                            $this->errors[$field] = "La fecha no tiene un formato válido (AAAA-MM-DD).";
                        break;

                    case 'number':
                    case 'int':
                        if (!is_numeric($value)) {
                            $this->errors[$field] = "Debe ser un valor numérico.";
                        } else {
                            if (isset($rule['min']) && $value < $rule['min']) $this->errors[$field] = "Valor mínimo: {$rule['min']}";
                            if (isset($rule['max']) && $value > $rule['max']) $this->errors[$field] = "Valor máximo: {$rule['max']}";
                        }
                        break;

                    case 'text':
                    case 'tel':
                        if (isset($rule['regex']) && !preg_match($rule['regex'], $value))
                            $this->errors[$field] = $rule['error'] ?? "Formato de texto no permitido.";
                        break;
                }
            }

            // Mapeo final: Guardamos el dato con el nombre de la columna original
            $this->data[$field] = $value;
        }
    }

    public function isValid() { return empty($this->errors); }
}