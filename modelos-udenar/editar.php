<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// editar.php
require_once 'config/Connectdb.php';

// Verificar que se recibió un ID válido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('ID no proporcionado');
}

// Usar el nombre de la primera columna como ID (debes conocerla o obtenerla)
// Como en table.php usaste la primera columna como identificador, aquí debes hacer lo mismo.
// Por simplicidad, asumiremos que la tabla tiene una columna 'id'. 
// Si no es así, reemplaza 'id' por el nombre real de tu columna primaria.
$id = intval($_GET['id']); // Forzamos a entero por seguridad

// Consultar el registro
$sql = "SELECT * FROM Clientes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Registro no encontrado');
}

$row = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Registro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Puedes copiar los estilos de header.php o crear unos específicos */
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .form-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            padding: 40px;
            width: 100%;
            max-width: 600px;
            animation: fadeInUp 0.6s ease-out;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        h2 {
            color: #1e293b;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #334155;
        }
        input, textarea, select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #5f72e4;
        }
        .btn-submit {
            background: linear-gradient(135deg, #5f72e4 0%, #824ad0 100%);
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(95, 114, 228, 0.4);
        }
        .btn-cancel {
            background: #f1f5f9;
            color: #334155;
            border: none;
            padding: 14px 30px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-left: 15px;
        }
        .btn-cancel:hover {
            background: #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2><i class="fas fa-edit"></i> Editar Registro #<?php echo $id; ?></h2>
        <form action="actualizar.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            
            <?php foreach ($row as $campo => $valor): ?>
                <?php if ($campo !== 'id'): // No mostramos el ID como campo editable ?>
                <div class="form-group">
                    <label for="<?php echo $campo; ?>"><?php echo ucfirst($campo); ?></label>
                    <input type="text" 
                           id="<?php echo $campo; ?>" 
                           name="<?php echo $campo; ?>" 
                           value="<?php echo htmlspecialchars($valor); ?>" 
                           required>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
            
            <div style="display: flex; align-items: center;">
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
                <a href="index.php" class="btn-cancel">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>