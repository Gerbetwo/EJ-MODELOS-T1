<!-- template.php -->
<?php
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sistema - Panel</title>

<!-- AdminLTE CSS -->
<link rel="stylesheet" href="/adminlte/dist/css/adminlte.min.css">

<!-- Font Awesome (si no lo llevas en adminlte/dist) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="" crossorigin="anonymous">

<!-- Bootstrap 4 (compatible con AdminLTE v3) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="" crossorigin="anonymous">

<!-- Tu CSS -->
<link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    <?php include __DIR__ . '/includes/navbar.php'; ?>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <div class="content-wrapper">
        <?php include __DIR__ . '/includes/alerts.php'; ?>
        <?= $content ?>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="adminlte/dist/js/adminlte.min.js"></script>
<script src="assets/scripts/scripts.js"></script>
</body>
</html>