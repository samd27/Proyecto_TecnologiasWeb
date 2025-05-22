<?php
require_once __DIR__ . '/../DataBase.php';

class Update {
    private $conn;

    public function __construct() {
        $db = new DataBase();
        $this->conn = $db->getConexion();
    }

    public function actualizarReporte($data) {
        try {
            if (
                empty($data['id']) ||
                empty($data['nombre_completo']) ||
                empty($data['correo_electronico']) ||
                empty($data['tipo_reporte']) ||
                empty($data['ubicacion']) ||
                empty($data['fecha_incidente'])
            ) {
                return false;
            }

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
