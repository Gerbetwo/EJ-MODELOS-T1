<?php
$page = $_GET['page'] ?? 'dashboard';

include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/navbar.php';

echo '<div class="content-wrapper">';
if ($page === 'clientes') {
    include 'controllers/ClientesController.php';
} else {
    include 'views/dashboard.php';
}
echo '</div>';

include 'includes/footer.php';
?>