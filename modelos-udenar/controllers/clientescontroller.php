<?php
require_once '../models/Cliente.php';
require_once '../config/connectdb.php';

$cliente = new Cliente($conn);
$action = $_GET['action'] ?? 'index';

switch($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            unset($data['id']);
            $cliente->create($data);
            header('Location: ../index.php?page=clientes&mensaje=creado');
        } else {
            include '../views/clientes/form.php';
        }
        break;
    case 'edit':
        $id = intval($_GET['id']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $cliente->update($id, $data);
            header('Location: ../index.php?page=clientes&mensaje=actualizado');
        } else {
            $row = $cliente->getById($id);
            include '../views/clientes/form.php';
        }
        break;
    case 'delete':
        $id = intval($_GET['id']);
        $cliente->delete($id);
        header('Location: ../index.php?page=clientes&mensaje=eliminado');
        break;
    default: // index
        $buscar = $_GET['buscar'] ?? '';
        $result = $cliente->getAll($buscar);
        include '../views/clientes/index.php';
}
$conn->close();
?>