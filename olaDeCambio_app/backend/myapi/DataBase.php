<?php
namespace App;

use PDO;
use PDOException;

class DataBase {
    private $conn;

    public function __construct() {
        try {
    $this->conn = new PDO("mysql:host=localhost;dbname=oladecambio;charset=utf8", "root", "samd2704");
    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

    }

    public function getConexion() {
        return $this->conn;
    }
}
