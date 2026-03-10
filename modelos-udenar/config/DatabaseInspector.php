<?php
// config/DatabaseInspector.php
class DatabaseInspector
{
    private $conn;
    private $dbName;

    public function __construct($mysqli)
    {
        $this->conn = $mysqli;
        // Obtenemos el nombre de la DB actual
        $res = $this->conn->query('SELECT DATABASE()');
        $row = $res->fetch_row();
        $this->dbName = $row[0];
    }

    public function getTables()
    {
        $tables = [];
        $res = $this->conn->query('SHOW TABLES');
        while ($row = $res->fetch_row()) {
            // Obtenemos conteo de registros por tabla para el Dashboard
            $countRes = $this->conn->query("SELECT COUNT(*) FROM `{$row[0]}`");
            $count = $countRes->fetch_row()[0];
            $tables[] = ['name' => $row[0], 'count' => $count];
        }
        return $tables;
    }

    public function getTableMetadata($tableName)
    {
        $columns = [];
        $res = $this->conn->query("SHOW FULL COLUMNS FROM `{$tableName}`");
        while ($row = $res->fetch_assoc()) {
            $columns[] = [
                'name' => $row['Field'],
                'type' => $row['Type'],
                'key' => $row['Key'],
                'null' => $row['Null'],
                'extra' => $row['Extra'],
            ];
        }
        return $columns;
    }
}
