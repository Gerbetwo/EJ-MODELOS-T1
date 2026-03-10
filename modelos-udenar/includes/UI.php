<?php
// incluye/UI.php

class UI
{
    public static function Table($headers, $data, $tableName)
    {
        $html = '<div class="table-responsive" style="border-radius: 0 0 12px 12px; overflow: hidden;">';
        $html .= '<table id="dynamicTable" class="table table-borderless table-hover text-white-custom mb-0" style="background: rgba(255,255,255,0.02);">';
        $html .= '<thead style="background: rgba(0,0,0,0.2); border-bottom: 1px solid rgba(168, 85, 247, 0.2);">';
        $html .= '<tr>';

        foreach ($headers as $h) {
            $html .= '<th class="text-accent py-3 px-4" style="letter-spacing: 1px; font-size: 0.75rem; font-weight: 700;">' . strtoupper($h) . '</th>';
        }

        $html .= '<th class="text-center text-accent py-3 px-4" style="font-size: 0.75rem;">ACCIONES</th></tr></thead><tbody id="tableBody">';

        if (empty($data)) {
            $html .= '<tr><td colspan="' . (count($headers) + 1) . '" class="text-center p-5 text-muted">No se encontraron registros</td></tr>';
        }

        foreach ($data as $row) {
            $html .= '<tr style="border-bottom: 1px solid rgba(255,255,255,0.03); transition: all 0.2s ease;">';
            foreach ($headers as $h) {
                $html .= '<td class="py-3 px-4 align-middle" style="color: rgba(255,255,255,0.8); font-size: 0.9rem;">' . htmlspecialchars($row[$h] ?? '-') . '</td>';
            }
            $html .= '<td class="text-center align-middle">
                <div class="btn-group">
                    <button class="btn btn-xs btn-outline-brand mr-2 btn-edit-js" 
                            style="border-radius: 6px; width: 32px; height: 32px;"
                            data-id="' . $row['id'] . '" 
                            data-table="' . $tableName . '">
                        <i class="fas fa-pen-nib"></i>
                    </button>
                    <a href="' . BASE_URL . $tableName . '/delete/' . $row['id'] . '" 
                       class="btn btn-xs btn-outline-danger" 
                       style="border-radius: 6px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;"
                       onclick="return confirm(\'¿Eliminar este registro permanentemente?\')">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </div>
            </td></tr>';
        }

        $html .= '</tbody></table></div>';
        return $html;
    }

    public static function Card($title, $content, $tableName = "")
    {
        // Botón de nuevo registro integrado en la Card
        $btnNuevo = "";
        if ($tableName && $tableName !== 'dashboard') {
            $btnNuevo = "
        <button class='btn btn-brand btn-sm ml-2 btn-new-js' data-table='$tableName'>
            <i class='fas fa-plus mr-1'></i> Nuevo
        </button>";
        }
        // Buscador ultra-limpio con estilo Glass
        $searchBar = '
        <div class="search-container mr-3">
            <div class="input-group input-group-sm" style="background: rgba(0,0,0,0.2); border-radius: 8px; border: 1px solid rgba(168, 85, 247, 0.3); padding: 2px 8px;">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-transparent border-0"><i class="fas fa-search text-brand" style="font-size: 0.8rem;"></i></span>
                </div>
                <input type="text" id="tableSearch" class="form-control bg-transparent border-0 text-white" placeholder="Filtrar datos..." style="box-shadow: none;">
            </div>
        </div>';

        return "
            <div class='card bg-surface border-glass shadow-glow'>
                <div class='card-header d-flex align-items-center border-0'>
                    <h3 class='card-title text-brand mb-0'>$title</h3>
                    <div class='card-tools ml-auto d-flex align-items-center'>
                        $searchBar
                        $btnNuevo
                    </div>
                </div>
                <div class='card-body p-0'>$content</div>
            </div>";
    }
}
