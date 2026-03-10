<?php
class Cliente extends GenericModel {
    public function __construct($mysqli) {
        parent::__construct($mysqli, 'Clientes');
    }

    public function searchCustom($termino) {
        $termino = $this->conn->real_escape_string($termino);
        $sql = "SELECT * FROM Clientes WHERE nombre LIKE '%$termino%'";
        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
}