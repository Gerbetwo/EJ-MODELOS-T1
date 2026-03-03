<?php
// models/clientes.php
require_once __DIR__ . '/../config/config.php';

class ClienteModel {
    private $conn;
    private $table;

    public function __construct($mysqli) {
        $this->conn = $mysqli; $this->table = defined('TABLE_CLIENTES') ? TABLE_CLIENTES :
'Clientes'; } public function getAll($buscar = '') { $sql = "SELECT * FROM `{$this->table}`"; if
($buscar !== '') { $buscar_esc = $this->conn->real_escape_string($buscar); // construir condiciones
para búsqueda sobre todas las columnas $cols = $this->getColumnNames(); $conds = []; foreach ($cols
as $c) { $conds[] = "`$c` LIKE '%$buscar_esc%'"; } if (!empty($conds)) $sql .= " WHERE " . implode("
OR ", $conds); } $res = $this->conn->query($sql); $rows = []; if ($res) { while ($r =
$res->fetch_assoc()) $rows[] = $r; $res->free(); } return $rows; } public function getById($id) {
$stmt = $this->conn->prepare("SELECT * FROM `{$this->table}` WHERE id = ?"); $stmt->bind_param('i',
$id); $stmt->execute(); $res = $stmt->get_result()->fetch_assoc(); $stmt->close(); return $res; }
public function create(array $data) { $cols = array_keys($data); $placeholders = implode(',',
array_fill(0, count($cols), '?')); $sql = "INSERT INTO `{$this->table}` (" . implode(',',
array_map(fn($c) => "`$c`", $cols)) . ") VALUES ($placeholders)"; $stmt =
$this->conn->prepare($sql); $types = str_repeat('s', count($cols)); $values = array_values($data);
$stmt->bind_param($types, ...$values); $ok = $stmt->execute(); $stmt->close(); return $ok; } public
function update($id, array $data) { $cols = array_keys($data); $set = implode('=?,',
array_map(fn($c) => "`$c`", $cols)) . '=?'; $sql = "UPDATE `{$this->table}` SET $set WHERE id = ?";
$stmt = $this->conn->prepare($sql); $types = str_repeat('s', count($cols)) . 'i'; $values =
array_values($data); $values[] = $id; $stmt->bind_param($types, ...$values); $ok = $stmt->execute();
$stmt->close(); return $ok; } public function delete($id) { $stmt = $this->conn->prepare("DELETE
FROM `{$this->table}` WHERE id = ?"); $stmt->bind_param('i', $id); $ok = $stmt->execute();
$stmt->close(); return $ok; } public function getColumns() { $cols = []; $res =
$this->conn->query("SHOW COLUMNS FROM `{$this->table}`"); if ($res) { while ($r =
$res->fetch_assoc()) $cols[] = $r; $res->free(); } return $cols; // cada elemento: Field, Type,
Null, Key, Default, Extra } public function getColumnNames() { $names = []; foreach
($this->getColumns() as $c) $names[] = $c['Field']; return $names; } }
