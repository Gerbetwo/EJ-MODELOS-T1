<?php
// includes/Components.php

class UI {
    public static function Card($title, $content, $icon = "fas fa-table") {
        return "
        <div class='card bg-surface shadow-glow mb-4'>
            <div class='card-header border-0'>
                <h3 class='card-title text-brand'><i class='$icon mr-2'></i>$title</h3>
            </div>
            <div class='card-body'>$content</div>
        </div>";
    }

    public static function Table($headers, $rows, $tableName) {
        $html = "<table class='table table-hover text-main'>";
        $html .= "<thead class='bg-void'><tr>";
        foreach ($headers as $h) $html .= "<th class='text-accent'>".strtoupper($h)."</th>";
        $html .= "<th class='text-center'>ACCIONES</th></tr></thead><tbody>";
        
        foreach ($rows as $row) {
            $html .= "<tr>";
            foreach ($row as $val) $html .= "<td>".htmlspecialchars($val)."</td>";
            $html .= "<td class='text-center'>
                <button class='btn btn-sm btn-outline-brand mr-1' onclick='editRecord(\"{$tableName}\", {$row['id']})'><i class='fas fa-edit'></i></button>
                <a href='index.php?table={$tableName}&action=delete&id={$row['id']}' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"¿Eliminar?\")'><i class='fas fa-trash'></i></a>
            </td></tr>";
        }
        $html .= "</tbody></table>";
        return $html;
    }
}