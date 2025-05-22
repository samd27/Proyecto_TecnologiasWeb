<?php

class DataBase {
    private $host = "localhost";
    private $db = "oladecambio"; // Cambiar por el nombre real de tu base
    private $user = "root";
    private $password = "samd2704";
    public $conexion;

    public function __construct() {
        $this->conexion = $this->conectar();
    }

    public function conectar() {
        try {
            $conexion = new PDO("mysql:host={$this->host};dbname={$this->db};charset=utf8", $this->user, $this->password);
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conexion;
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public function getConexion() {
        return $this->conexion;
    }
}
