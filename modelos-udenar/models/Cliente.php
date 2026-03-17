<?php
class Cliente extends GenericModel
{
    public function __construct($mysqli)
    {
        parent::__construct($mysqli, 'clientes');
    }

    public function searchCustom($termino)
    {
        $termino = $this->conn->real_escape_string($termino);
        // Ahora respeta el nombre dinámico de la tabla
        $sql = "SELECT * FROM `{$this->table}` WHERE nombre LIKE '%$termino%'";
        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
}
