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
        echo "<div class='container'><div class='alert $class alert-dismissible fade show' role='alert'>$txt<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div></div>";
    }
}
