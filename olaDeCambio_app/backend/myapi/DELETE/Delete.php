<?php
require_once __DIR__ . '/../DataBase.php';

class Delete {
    private $conn;

    public function __construct() {
        $db = new DataBase();
        $this->conn = $db->getConexion();
    }

    public function eliminar($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM reportes WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
