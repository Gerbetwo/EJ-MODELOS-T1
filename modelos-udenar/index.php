<?php
// index.php
require_once 'config/connectdb.php';

// Determinar la página a cargar
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$pageFile = "pages/$page.php";

// Validar existencia de la página
if (!file_exists($pageFile)) {
    $pageFile = "pages/404.php"; // Página de error
}

// Header + AdminLTE CSS
include 'includes/header.php';

// Sidebar y Navbar
include 'includes/sidebar.php';
include 'includes/navbar.php';

// Contenedor principal
echo '<div class="content-wrapper">';
include $pageFile;
echo '</div>';

// Footer
include 'includes/footer.php';
$conn->close();
?>