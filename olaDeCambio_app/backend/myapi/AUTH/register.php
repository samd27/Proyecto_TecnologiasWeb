<?php
namespace App\AUTH;

use App\DataBase;
use PDO;
use PDOException;

class Register {
    public function registrarUsuario(array $data): array {
        if (empty($data['usuario']) || empty($data['contrasena'])) {
            return ['success' => false, 'message' => 'Datos incompletos'];
        }

        $db = new DataBase();
        $conn = $db->getConexion();

        if (!$conn) {
            return ['success' => false, 'message' => 'Error de conexión'];
        }

        try {
            $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
            $stmt->bindParam(':usuario', $data['usuario']);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Usuario ya existe'];
            }

            $hash = password_hash($data['contrasena'], PASSWORD_DEFAULT);

            $insert = $conn->prepare("INSERT INTO usuarios (usuario, contrasena) VALUES (:usuario, :contrasena)");
            $insert->bindParam(':usuario', $data['usuario']);
            $insert->bindParam(':contrasena', $hash);
            $insert->execute();

            return ['success' => true, 'message' => 'Registro exitoso'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error en el servidor'];
        }
    }
}
