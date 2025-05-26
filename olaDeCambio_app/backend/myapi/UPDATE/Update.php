<?php
namespace App\UPDATE;

use App\DataBase;
use PDO;
use PDOException;

class Update {
    private $conn;

    public function __construct() {
        $db = new DataBase();
        $this->conn = $db->getConexion();
    }

    public function actualizarReporte($data) {
        try {
            $sql = "UPDATE reportes SET 
                        nombre_completo = :nombre,
                        correo_electronico = :correo,
                        tipo_reporte = :tipo,
                        ubicacion = :ubicacion,
                        descripcion_detallada = :descripcion,
                        fecha_incidente = :fecha
                    WHERE id = :id";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindParam(':nombre', $data['nombre_completo']);
            $stmt->bindParam(':correo', $data['correo_electronico']);
            $stmt->bindParam(':tipo', $data['tipo_reporte']);
            $stmt->bindParam(':ubicacion', $data['ubicacion']);
            $stmt->bindParam(':descripcion', $data['descripcion_detallada']);
            $stmt->bindParam(':fecha', $data['fecha_incidente']);
            $stmt->bindParam(':id', $data['id']);

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
