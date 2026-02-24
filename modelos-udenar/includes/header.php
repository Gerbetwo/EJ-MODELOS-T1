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

<style>
    .data-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    .data-table th, .data-table td { padding: 12px; border: 1px solid #ddd; text-align: left; }
    .data-table th { background-color: #f4f4f4; }
    .btn-edit { color: #2196F3; cursor: pointer; border: none; background: none; transition: 0.3s; }
    .btn-delete { color: #f44336; cursor: pointer; border: none; background: none; transition: 0.3s; }
    .btn-edit:hover, .btn-delete:hover { transform: scale(1.2); }
    .btn-nuevo {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 10px 20px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    backdrop-filter: blur(5px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease;
    
}
.btn-nuevo:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.02);
}
.alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin: 20px 30px 0 30px;
    font-weight: 500;
    animation: slideDown 0.3s ease-out;
}
.alert-success {
    background: #d4edda;
    color: #155724;
    border-left: 4px solid #28a745;
}
.alert-info {
    background: #d1ecf1;
    color: #0c5460;
    border-left: 4px solid #17a2b8;
}
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}
.search-form {
    display: flex;
    align-items: center;
    background: rgba(255,255,255,0.2);
    border-radius: 50px;
    padding: 5px 5px 5px 15px;
    backdrop-filter: blur(5px);
    border: 1px solid rgba(255,255,255,0.3);
    flex: 1;
    max-width: 400px;
    margin: 0 20px;
}
.search-form input {
    background: transparent;
    border: none;
    color: white;
    font-family: 'Inter', sans-serif;
    font-size: 0.95rem;
    padding: 8px 0;
    width: 100%;
    outline: none;
}
.search-form input::placeholder {
    color: rgba(255,255,255,0.7);
}
.search-form button {
    background: white;
    border: none;
    color: #5f72e4;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}
.search-form button:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}
</style>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            background: linear-gradient(135deg, #5f72e4 0%, #824ad0 100%);
            padding: 25px 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-weight: 700;
            font-size: 1.8rem;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header h1 i {
            font-size: 2rem;
        }

        .user-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .user-badge:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.02);
        }

        .user-badge i {
            font-size: 1.2rem;
        }

        .table-wrapper {
            padding: 30px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        thead {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }

        th {
            padding: 16px 20px;
            text-align: left;
            font-weight: 600;
            color: #1e293b;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        tbody tr {
            background: white;
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.3s ease;
            animation: slideIn 0.4s ease-out forwards;
            opacity: 0;
            transform: translateX(-10px);
        }

        tbody tr:nth-child(1) { animation-delay: 0.1s; }
        tbody tr:nth-child(2) { animation-delay: 0.2s; }
        tbody tr:nth-child(3) { animation-delay: 0.3s; }
        tbody tr:nth-child(4) { animation-delay: 0.4s; }
        tbody tr:nth-child(5) { animation-delay: 0.5s; }
        tbody tr:nth-child(6) { animation-delay: 0.6s; }
        tbody tr:nth-child(7) { animation-delay: 0.7s; }
        tbody tr:nth-child(8) { animation-delay: 0.8s; }
        tbody tr:nth-child(9) { animation-delay: 0.9s; }
        tbody tr:nth-child(10) { animation-delay: 1s; }

        @keyframes slideIn {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        tbody tr:hover {
            background: #f1f5f9;
            transform: scale(1.01);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        td {
            padding: 16px 20px;
            color: #334155;
        }

        .actions {
            display: flex;
            gap: 12px;
        }

        .btn-edit, .btn-delete {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.2s ease;
            padding: 8px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .btn-edit {
            color: #3b82f6;
            background: rgba(59, 130, 246, 0.1);
        }

        .btn-edit:hover {
            background: #3b82f6;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(59, 130, 246, 0.4);
        }

        .btn-delete {
            color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
        }

        .btn-delete:hover {
            background: #ef4444;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(239, 68, 68, 0.4);
        }

        .empty-message {
            text-align: center;
            padding: 40px;
            color: #64748b;
            font-size: 1.2rem;
        }

        .empty-message i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #cbd5e1;
        }

        .footer {
            background: #f8fafc;
            padding: 20px 30px;
            text-align: center;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            font-size: 0.9rem;
        }

        /* Scrollbar personalizada */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Efecto ripple */
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: ripple-animation 0.6s ease-out;
            pointer-events: none;
        }
        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <i class="fas fa-database"></i> 
                Modelo Udenar
            </h1>
            
            <!-- Buscador -->
            <form action="index.php" method="GET" class="search-form">
                <input type="text" name="buscar" placeholder="Buscar cliente..." 
                    value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
            <div style="display: flex; align-items: center; gap: 15px;">
                <a href="crear.php" class="btn-nuevo">
                    <i class="fas fa-plus-circle"></i> Nuevo Cliente
                </a>
                <div class="user-badge">
                    <i class="fas fa-user-circle"></i>
                    <span>Gebert</span>
                    <i class="fas fa-chevron-down" style="font-size: 0.8rem;"></i>
                </div>
            </div>
        </div>
        <div class="table-wrapper">