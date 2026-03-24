<?php

declare(strict_types=1);

namespace App\Presentation\Middleware;

/**
 * Middleware de protección CSRF (Cross-Site Request Forgery).
 *
 * **Capa:** Presentación (Anillo 4)
 *
 * **¿Qué es CSRF?**
 * Un ataque donde un sitio externo engaña al usuario para enviar un formulario
 * a TU servidor. Ejemplo: un sitio malicioso tiene un `<form action="tu-app/delete/1">`.
 * Si tu usuario está logueado, el navegador envía las cookies de sesión y
 * el servidor lo ejecuta sin saber que fue un ataque.
 *
 * **¿Cómo lo previene un token?**
 * 1. Al cargar el formulario, generamos un token aleatorio y lo guardamos en sesión.
 * 2. El formulario incluye el token como `<input type="hidden" name="_csrf_token">`.
 * 3. Al recibir el POST, comparamos el token del formulario con el de la sesión.
 * 4. Si no coinciden → es un ataque CSRF → rechazamos la petición.
 *
 * Un sitio externo no puede leer nuestro token (Same-Origin Policy del navegador).
 */
final class CsrfMiddleware
{
    private const TOKEN_KEY = '_csrf_token';

    /**
     * Genera un token CSRF y lo almacena en sesión.
     *
     * @return string Token generado (para incluir en el formulario)
     */
    public function generateToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION[self::TOKEN_KEY] = $token;

        return $token;
    }

    /**
     * Valida que el token del formulario coincida con el de sesión.
     *
     * @param string $submittedToken Token enviado por el formulario
     * @return bool true si el token es válido
     */
    public function validateToken(string $submittedToken): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $storedToken = $_SESSION[self::TOKEN_KEY] ?? '';

        if ($storedToken === '' || $submittedToken === '') {
            return false;
        }

        // hash_equals() es timing-safe — previene ataques de timing side-channel
        $valid = hash_equals($storedToken, $submittedToken);

        // Invalida el token después de usarlo (one-time use)
        unset($_SESSION[self::TOKEN_KEY]);

        return $valid;
    }

    /**
     * Obtiene el token actual de sesión (o genera uno nuevo).
     */
    public function getToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION[self::TOKEN_KEY])) {
            return $this->generateToken();
        }

        return $_SESSION[self::TOKEN_KEY];
    }
}
