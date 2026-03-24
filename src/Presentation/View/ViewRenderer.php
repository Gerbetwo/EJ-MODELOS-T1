<?php

declare(strict_types=1);

namespace App\Presentation\View;

/**
 * Renderizador de vistas para la capa de presentación.
 *
 * Cumple con el Principio de Responsabilidad Única (SRP) extrayendo la lógica
 * de renderizado fuera de los controladores HTTP.
 */
class ViewRenderer
{
    /**
     * @param string $viewsDir El directorio base donde se encuentran las vistas.
     */
    public function __construct(
        private readonly string $viewsDir = __DIR__ . '/../../../views'
    ) {
    }

    /**
     * Renderiza una vista .phtml inyectando un arreglo asociativo de variables.
     * Soporta fallback automático (específica -> genérica -> default).
     *
     * @param string $moduleSlug El slug del módulo (ej: 'clientes', 'orders')
     * @param string $template   El nombre de la plantilla sin extensión (ej: 'list', 'form')
     * @param array  $props      Variables a inyectar en la vista
     * @return string
     */
    public function render(string $moduleSlug, string $template, array $props = []): string
    {
        extract($props);
        ob_start();

        $specificView = "{$this->viewsDir}/{$moduleSlug}/{$template}.phtml";
        $genericView = "{$this->viewsDir}/generic/{$template}.phtml";

        if (file_exists($specificView)) {
            include $specificView;
        } elseif (file_exists($genericView)) {
            include $genericView;
        } else {
            // Limpiamos el buffer si no se encontró nada para evitar salidas raras
            ob_end_clean();
            throw new \RuntimeException("No template found for {$moduleSlug}/{$template}");
        }

        return (string) ob_get_clean();
    }
}
