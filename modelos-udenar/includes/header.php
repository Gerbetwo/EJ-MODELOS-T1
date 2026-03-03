<?php
// includes/header.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Modelo Udenar</title>
    <!-- Fuentes y estilos profesionales -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <i class="fas fa-database"></i> 
                Modelo de la Computacion
            </h1>
            
            <!-- Buscador dinámico -->
            <form id="searchForm" class="search-form" onsubmit="return false;">
                <input type="text" id="buscar" placeholder="Buscar cliente...">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
            
            <!-- Cliente -->
            <div style="display: flex; align-items: center; gap: 15px;">
                <button class="btn-nuevo" onclick="nuevoRegistro()">
                    <i class="fas fa-plus-circle"></i> Nuevo Cliente
                </button>
                <div class="user-badge">
                    <i class="fas fa-user-circle"></i>
                    <span>Gebert</span>
                    <i class="fas fa-chevron-down" style="font-size: 0.8rem;"></i>
                </div>
            </div>
        </div>
        <div class="table-wrapper">