<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Udenar | Gestión Modular</title>

    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.0/css/all.css">
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
    <div class="modal fade" id="modalGenérico" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content bg-surface border-glass shadow-glow">
                <div class="modal-header border-glass">
                    <h5 class="modal-title text-brand" id="modalTitle">Formulario</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="modalBody" class="modal-body">
                </div>
            </div>
        </div>
    </div>
    <script>
        // Pasamos las constantes de PHP a un objeto global de JS
        const CONFIG = {
            baseUrl: '<?= BASE_URL ?>',
            currentUrl: '<?= $_GET['url'] ?? "dashboard" ?>'
        };
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    <script src="<?= BASE_URL ?>assets/scripts/scripts.js"></script>
    <script src="<?= BASE_URL ?>assets/scripts/neural-background.js"></script>
</body>

</html>