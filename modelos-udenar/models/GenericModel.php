<?php
class GenericModel
{
    protected $conn;
    protected $table;

    public function __construct($mysqli, $table)
    {
        $this->conn = $mysqli;
        $this->table = $this->conn->real_escape_string($table);
    }

    public function getAll()
    {
        $res = $this->conn->query("SELECT * FROM `{$this->table}`");
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM `{$this->table}` WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM `{$this->table}` WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public function save($data, $id = null)
    {
        $cols = array_keys($data);
        $vals = array_values($data);
        $types = str_repeat('s', count($vals));

        if ($id) {
            // Update
            $set = implode('=?, ', $cols) . '=?';
            $stmt = $this->conn->prepare("UPDATE `{$this->table}` SET $set WHERE id = ?");
            $vals[] = $id;
            $types .= 'i';
            $stmt->bind_param($types, ...$vals);
        } else {
            // Insert
            $placeholders = implode(',', array_fill(0, count($cols), '?'));
            $colNames = implode(',', array_map(fn($c) => "`$c`", $cols));
            $stmt = $this->conn->prepare(
                "INSERT INTO `{$this->table}` ($colNames) VALUES ($placeholders)",
            );
            $stmt->bind_param($types, ...$vals);
        }
        return $stmt->execute();
    }

    public function getAllRelational($joinTable, $foreignKey, $displayColumn)
    {
        $sql = "SELECT t1.*, t2.$displayColumn as relation_name 
            FROM `{$this->table}` t1
            LEFT JOIN `$joinTable` t2 ON t1.$foreignKey = t2.id";
        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function where($field, $value)
    {
        $stmt = $this->conn->prepare("SELECT * FROM `{$this->table}` WHERE `$field` = ? LIMIT 1");
        $stmt->bind_param('s', $value);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
