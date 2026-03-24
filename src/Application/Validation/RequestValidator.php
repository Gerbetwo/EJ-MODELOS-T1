<?php

declare(strict_types=1);

namespace App\Application\Validation;

/**
 * Validador de datos de entrada contra reglas definidas en Atributos PHP 8.
 *
 * **Capa:** Aplicación (Anillo 2)
 *
 * **Renombrado:** Antes se llamaba `RequestDTO` y tenía dos responsabilidades:
 * 1. Validar datos
 * 2. Transportar datos (DTO)
 *
 * Ahora tiene UNA sola responsabilidad: validar. Los datos validados se
 * retornan como un array simple. Esto sigue el Single Responsibility Principle.
 *
 * Depende solo de tipos primitivos de PHP (arrays, strings). No conoce
 * PDO, HTTP, ni ninguna clase de Infraestructura.
 */
final class RequestValidator
{
    /** @var array<string, mixed> Datos validados y sanitizados */
    public readonly array $data;

    /** @var array<string, string> Errores de validación (campo => mensaje) */
    public readonly array $errors;

    /**
     * @param array<string, mixed> $rawData Datos crudos
     * @param array<string, array<string, mixed>> $rules Reglas de validación
     */
    public function __construct(array $rawData, array $rules)
    {
        $data = [];
        $errors = [];

        $normalizedInput = array_change_key_case($rawData, CASE_LOWER);

        foreach ($rules as $field => $rule) {
            $fieldLower = strtolower($field);
            $value = isset($normalizedInput[$fieldLower])
                ? trim((string) $normalizedInput[$fieldLower])
                : null;

            $isRequired = $rule['required'] ?? true;

            if ($isRequired && ($value === null || $value === '')) {
                $errors[$field] = 'El campo ' . ucfirst($field) . ' es obligatorio.';
                continue;
            }

            if ($value !== null && $value !== '') {
                $error = $this->validateByType($value, $rule);
                if ($error !== null) {
                    $errors[$field] = $error;
                }
            }

            $data[$field] = $value;
        }

        $this->data = $data;
        $this->errors = $errors;
    }

    public function isValid(): bool
    {
        return empty($this->errors);
    }

    private function validateByType(string $value, array $rule): ?string
    {
        $type = $rule['type'] ?? 'text';

        return match ($type) {
            'email'        => $this->validateEmail($value, $rule),
            'date'         => $this->validateDate($value),
            'number', 'int' => $this->validateNumber($value, $rule),
            'text', 'tel'  => $this->validateText($value, $rule),
            'relation'     => null,
            default        => null,
        };
    }

    private function validateEmail(string $value, array $rule): ?string
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return $rule['error'] ?? 'Formato de correo inválido.';
        }
        return null;
    }

    private function validateDate(string $value): ?string
    {
        $date = \DateTime::createFromFormat('Y-m-d', $value);
        if (!($date && $date->format('Y-m-d') === $value)) {
            return 'La fecha no tiene un formato válido (AAAA-MM-DD).';
        }
        return null;
    }

    private function validateNumber(string $value, array $rule): ?string
    {
        if (!is_numeric($value)) {
            return 'Debe ser un valor numérico.';
        }
        $numericValue = (float) $value;
        if (isset($rule['min']) && $numericValue < $rule['min']) {
            return "Valor mínimo: {$rule['min']}";
        }
        if (isset($rule['max']) && $numericValue > $rule['max']) {
            return "Valor máximo: {$rule['max']}";
        }
        return null;
    }

    private function validateText(string $value, array $rule): ?string
    {
        if (isset($rule['regex']) && !preg_match($rule['regex'], $value)) {
            return $rule['error'] ?? 'Formato de texto no permitido.';
        }
        if (isset($rule['minlength']) && mb_strlen($value) < $rule['minlength']) {
            return "Mínimo {$rule['minlength']} caracteres.";
        }
        return null;
    }
}
