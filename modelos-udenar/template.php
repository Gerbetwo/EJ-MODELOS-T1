<?php
// template.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema - Panel</title>

    <!-- AdminLTE -->
    <link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom -->
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <?php include __DIR__ . '/includes/navbar.php'; ?>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <div class="content-wrapper p-3">
        <?php include __DIR__ . '/includes/alerts.php'; ?>
        <?= $content ?>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="adminlte/dist/js/adminlte.min.js"></script>
<script src="assets/scripts/scripts.js"></script>
</body>
</html>