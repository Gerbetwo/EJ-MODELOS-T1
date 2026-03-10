<?php
// includes/UI.php

class UI {
    // Componente: Tabla Dinámica
    public static function Table($headers, $data, $tableName) {
        $html = '<div class="table-responsive">';
        $html .= '<table class="table table-hover text-white-custom mb-0">';
        $html .= '<thead class="bg-void"><tr>';
        
        foreach ($headers as $h) {
            $html .= '<th class="text-accent border-bottom-0">' . strtoupper($h) . '</th>';
        }
        
        $html .= '<th class="text-center border-bottom-0">ACCIONES</th></tr></thead><tbody>';

        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($headers as $h) {
                $html .= '<td>' . htmlspecialchars($row[$h] ?? '') . '</td>';
            }
            // Botones de acción dinámicos
            $html .= '<td class="text-center">
                <button class="btn btn-xs btn-outline-brand mr-1" onclick="openEditModal(\''.$tableName.'\', '.$row['id'].')">
                    <i class="fas fa-pen"></i>
                </button>
                <a href="index.php?table='.$tableName.'&action=delete&id='.$row['id'].'" 
                   class="btn btn-xs btn-outline-danger" 
                   onclick="return confirm(\'¿Eliminar?\')">
                    <i class="fas fa-trash"></i>
                </a>
            </td></tr>';
        }

        $html .= '</tbody></table></div>';
        return $html;
    }

    // Componente: Card de Contenedor
    public static function Card($title, $content, $tools = "") {
        return "
        <div class='card bg-surface border-glass shadow-glow'>
            <div class='card-header d-flex align-items-center'>
                <h3 class='card-title text-brand mb-0'>$title</h3>
                <div class='card-tools ml-auto'>$tools</div>
            </div>
            <div class='card-body p-0'>$content</div>
        </div>";
    }
}