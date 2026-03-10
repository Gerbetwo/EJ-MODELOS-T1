<?php
// includes/alerts.php
if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
    $txt = '';
    $class = 'alert-info';
    switch ($msg) {
        case 'creado':
            $txt = 'Registro creado correctamente.';
            $class = 'alert-success';
            break;
        case 'actualizado':
            $txt = 'Registro actualizado correctamente.';
            $class = 'alert-success';
            break;
        case 'eliminado':
            $txt = 'Registro eliminado correctamente.';
            $class = 'alert-success';
            break;
        case 'error':
            $txt = 'Ocurrió un error.';
            $class = 'alert-danger';
            break;
    }
    if ($txt !== '') {
        // Estructura BS4: se usa data-dismiss (sin el -bs-) y la clase 'close'
        echo "
        <div class='alert $class alert-dismissible fade show alert-custom' role='alert'>
            $txt
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                <span aria-hidden='true' style='color: white;'>&times;</span>
            </button>
        </div>";
    }
}
