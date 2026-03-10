<?php
// models/Cliente.php

class ClienteModel extends GenericModel {
    // Ya no necesitas escribir el constructor ni el save/delete
    // porque ya los tiene GenericModel.

    public function __construct($mysqli) {
        parent::__construct($mysqli, 'Clientes');
    }

    // Solo escribes lo que es ÚNICO para Clientes
    public function searchCustom($termino) {
        $termino = $this->conn->real_escape_string($termino);
        $sql = "SELECT * FROM Clientes WHERE nombre LIKE '%$termino%' OR nit LIKE '%$termino%'";
        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
}