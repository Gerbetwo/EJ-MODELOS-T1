<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

/**
 * Encapsula el acceso a variables de entorno con tipado fuerte.
 *
 * **Capa:** Infraestructura (Anillo 3)
 *
 * **¿Por qué no acceder a $_ENV directamente?**
 * 1. `$_ENV` es un array sin tipos — `$_ENV['DB_PORT']` retorna string, no int.
 * 2. No valida que las variables requeridas existan — un typo en `$_ENV['DB_HSOT']`
 *    retorna null silenciosamente.
 * 3. Si cambias la fuente de configuración (de .env a YAML, AWS SSM, etc.),
 *    tendrías que buscar y reemplazar en todo el código.
 *
 * Con `EnvConfig`, todo eso se maneja en un solo lugar con tipado.
 */
final readonly class EnvConfig
{
    public string $dbHost;
    public string $dbName;
    public string $dbUser;
    public string $dbPassword;
    public int $dbPort;

    /**
     * Carga configuración desde $_ENV (previamente poblado por phpdotenv).
     *
     * @throws \RuntimeException Si falta alguna variable requerida
     */
    public function __construct()
    {
        $this->dbHost = $this->requireEnv('DB_HOST');
        $this->dbName = $this->requireEnv('DB_NAME');
        $this->dbUser = $this->requireEnv('DB_USER');
        $this->dbPassword = $this->requireEnv('DB_PASS');
        $this->dbPort = (int) ($this->getEnv('DB_PORT') ?? '3306');
    }

    /**
     * Lee una variable obligatoria de $_ENV.
     *
     * @throws \RuntimeException Si la variable no existe
     */
    private function requireEnv(string $key): string
    {
        $value = $this->getEnv($key);
        if ($value === null || $value === '') {
            throw new \RuntimeException(
                "Variable de entorno requerida no encontrada: {$key}. "
                . 'Verifica tu archivo .env'
            );
        }
        return $value;
    }

    /**
     * Lee una variable opcional de $_ENV.
     */
    private function getEnv(string $key): ?string
    {
        return $_ENV[$key] ?? null;
    }
}
