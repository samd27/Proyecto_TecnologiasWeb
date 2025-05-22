<?php
require_once __DIR__ . '/../DataBase.php';

class Read {
    private $conn;

    public function __construct() {
        $db = new DataBase();
        $this->conn = $db->getConexion();
    }

    public function obtenerReportes() {
        try {
            $stmt = $this->conn->query("SELECT * FROM reportes ORDER BY id DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
