<?php
namespace App\DELETE;

use App\DataBase;
use PDO;

class Delete {
    private $conn;

    public function __construct() {
        $db = new DataBase();
        $this->conn = $db->getConexion();
    }

    public function eliminar($id) {
        $stmt = $this->conn->prepare("DELETE FROM reportes WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
