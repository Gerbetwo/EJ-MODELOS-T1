<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Udenar | Gestión Modular</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/vendor/adminlte.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/vendor/all.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/custom/main.css" />
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="neural-background">
        <canvas id="neuralCanvas" class="neural-canvas"></canvas>
    </div>

    <div class="wrapper">
        <?php include 'includes/Navbar.php'; ?>
        <?php include 'includes/Sidebar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-white" style="text-shadow: 2px 2px 4px">
                                <?= ucfirst($tableName ?? 'Dashboard') ?>
                            </h1>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <?php include 'includes/Alerts.php'; ?>
                    <?= $content ?>
                </div>
            </section>
        </div>

        <?php include 'includes/Footer.php'; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    <script src="<?= BASE_URL ?>assets/scripts/scripts.js"></script>
    <script src="<?= BASE_URL ?>assets/scripts/neural-background.js"></script>
</body>

</html>