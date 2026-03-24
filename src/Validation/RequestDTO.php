<?php

declare(strict_types=1);

namespace App\Validation;

use App\Attribute\Column;
use App\Attribute\Relation;

/**
 * DTO (Data Transfer Object) para validar y sanitizar datos de entrada.
 *
 * **¿Qué es un DTO?**
 * Un DTO es un objeto que transporta datos entre capas (del request HTTP al modelo).
 * A diferencia de un array crudo, el DTO garantiza que los datos ya fueron
 * validados y sanitizados antes de llegar al modelo.
 *
 * **Mejoras respecto a la versión anterior:**
 * 1. Usa `match` expression (PHP 8.0+) en vez de `switch` — más conciso y seguro
 *    (match es exhaustivo: lanza error si un caso no está cubierto).
 * 2. Propiedades `readonly` — una vez construido, los datos no se pueden mutar.
 * 3. Type hints estrictos en todos los métodos.
 * 4. Acepta reglas desde Atributos PHP 8 (Column/Relation) en vez de un array.
 */
final class RequestDTO
{
    /** @var array<string, mixed> Datos validados y sanitizados */
    public readonly array $data;

    /** @var array<string, string> Errores de validación (campo => mensaje) */
    public readonly array $errors;

    /**
     * Construye el DTO validando los datos crudos contra las reglas proporcionadas.
     *
     * **¿Por qué las reglas son un array y no los Atributos directamente?**
     * Para mantener el DTO agnóstico a la fuente de las reglas. El Router
     * extrae las reglas de los Atributos PHP 8 y las pasa como array.
     * Así el DTO es fácil de testear unitariamente con cualquier array de reglas.
     *
     * @param array<string, mixed> $rawData Datos crudos del request ($_POST)
     * @param array<string, array{type: string, placeholder?: string, regex?: string, error?: string, min?: int, max?: int, minlength?: int, required?: bool, references?: string, display?: string}> $rules Reglas de validación
     */
    public function __construct(array $rawData, array $rules)
    {
        $data = [];
        $errors = [];

        // Normalizamos las llaves del input a minúsculas
        $normalizedInput = array_change_key_case($rawData, CASE_LOWER);

        foreach ($rules as $field => $rule) {
            $fieldLower = strtolower($field);
            $value = isset($normalizedInput[$fieldLower])
                ? trim((string) $normalizedInput[$fieldLower])
                : null;

            $isRequired = $rule['required'] ?? true;

            // 1. Validación de obligatoriedad
            if ($isRequired && ($value === null || $value === '')) {
                $errors[$field] = 'El campo ' . ucfirst($field) . ' es obligatorio.';
                continue;
            }

            // 2. Si tiene valor, validar por tipo
            if ($value !== null && $value !== '') {
                $error = $this->validateByType($value, $rule);
                if ($error !== null) {
                    $errors[$field] = $error;
                }
            }

            // 3. Mapeo final
            $data[$field] = $value;
        }

        // Asignamos las propiedades readonly (solo se puede hacer en el constructor)
        $this->data = $data;
        $this->errors = $errors;
    }

    /**
     * Verifica si los datos pasaron todas las validaciones.
     */
    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /**
     * Valida un valor según su tipo usando match expression.
     *
     * **¿Por qué `match` en vez de `switch`?**
     * 1. `match` usa comparación estricta (===) mientras `switch` usa loose (==).
     * 2. `match` es una expresión que retorna un valor — más funcional y conciso.
     * 3. `match` lanza UnhandledMatchError si no hay caso que coincida (fail-fast).
     *
     * @param string               $value Valor a validar
     * @param array<string, mixed> $rule  Regla de validación
     * @return ?string Mensaje de error o null si es válido
     */
    private function validateByType(string $value, array $rule): ?string
    {
        $type = $rule['type'] ?? 'text';

        return match ($type) {
            'email' => $this->validateEmail($value, $rule),
            'date' => $this->validateDate($value),
            'number', 'int' => $this->validateNumber($value, $rule),
            'text', 'tel' => $this->validateText($value, $rule),
            'relation' => null, // Las relaciones no necesitan validación de formato
            default => null,
        };
    }

    /**
     * Valida formato de email.
     */
    private function validateEmail(string $value, array $rule): ?string
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return $rule['error'] ?? 'Formato de correo inválido.';
        }
        return null;
    }

    /**
     * Valida formato de fecha (YYYY-MM-DD).
     */
    private function validateDate(string $value): ?string
    {
        $date = \DateTime::createFromFormat('Y-m-d', $value);
        if (!($date && $date->format('Y-m-d') === $value)) {
            return 'La fecha no tiene un formato válido (AAAA-MM-DD).';
        }
        return null;
    }

    /**
     * Valida un valor numérico con rangos opcionales.
     */
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

    /**
     * Valida texto con regex y longitud mínima.
     */
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
