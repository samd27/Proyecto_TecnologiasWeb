<?php
require_once __DIR__ . '/../DataBase.php';

class Create {
    private $conn;

    public function __construct() {
        $db = new DataBase();
        $this->conn = $db->getConexion();
    }

    public function crearReporte($data) {
        try {
            // Validar campos requeridos
            if (
                empty($data['nombre_completo']) ||
                empty($data['correo_electronico']) ||
                empty($data['tipo_reporte']) ||
                empty($data['ubicacion']) ||
                empty($data['fecha_incidente'])
            ) {
                return false;
            }

            $sql = "INSERT INTO reportes (nombre_completo, correo_electronico, tipo_reporte, ubicacion, descripcion_detallada, fecha_incidente)
        VALUES (:nombre, :correo, :tipo, :ubicacion, :descripcion, :fecha)";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindParam(':nombre', $data['nombre_completo']);
            $stmt->bindParam(':correo', $data['correo_electronico']);
            $stmt->bindParam(':tipo', $data['tipo_reporte']);
            $stmt->bindParam(':ubicacion', $data['ubicacion']);
            $stmt->bindParam(':descripcion', $data['descripcion_detallada']);
            $stmt->bindParam(':fecha', $data['fecha_incidente']);

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
