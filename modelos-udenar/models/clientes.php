<?php
class Cliente {
    private $conn;
    private $table = 'Clientes';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($buscar = '') {
        $sql = "SELECT * FROM $this->table";
        if ($buscar) {
            $cols = [];
            $resCols = $this->conn->query("SHOW COLUMNS FROM $this->table");
            while ($c = $resCols->fetch_assoc()) $cols[] = $c['Field'];
            $cond = array_map(fn($col) => "$col LIKE '%" . $this->conn->real_escape_string($buscar) . "%'", $cols);
            $sql .= " WHERE " . implode(" OR ", $cond);
        }
        return $this->conn->query($sql);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $res;
    }

    public function create($data) {
        $cols = array_keys($data);
        $placeholders = implode(',', array_fill(0, count($cols), '?'));
        $sql = "INSERT INTO $this->table (" . implode(',', $cols) . ") VALUES ($placeholders)";
        $stmt = $this->conn->prepare($sql);
        $tipos = str_repeat('s', count($cols));
        $stmt->bind_param($tipos, ...array_values($data));
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function update($id, $data) {
        $cols = array_keys($data);
        $set = implode('=?,', $cols) . '=?';
        $sql = "UPDATE $this->table SET $set WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        $tipos = str_repeat('s', count($cols)) . 'i';
        $stmt->bind_param($tipos, ...array_values($data), $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM $this->table WHERE id=?");
        $stmt->bind_param('i', $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}
?>