<?php if (isset($_GET['mensaje'])): 
    $mensaje = $_GET['mensaje'];
    $clase = $texto = '';
    switch($mensaje) {
        case 'creado': $clase='success'; $texto='Cliente creado correctamente.'; break;
        case 'actualizado': $clase='success'; $texto='Registro actualizado correctamente.'; break;
        case 'eliminado': $clase='info'; $texto='Registro eliminado correctamente.'; break;
    }
?>
<div class="alert alert-<?= $clase ?> mt-2"><?= $texto ?></div>
<?php endif; ?>