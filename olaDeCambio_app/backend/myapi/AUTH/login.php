<?php
namespace App\AUTH;

use App\DataBase;
use PDO;
use PDOException;

class Login {
    public function iniciarSesion(array $data): array {
        session_start();

        if (empty($data['username']) || empty($data['password'])) {
            return ['success' => false, 'message' => 'Faltan datos'];
        }

        $db = new DataBase();
        $conn = $db->getConexion();

        if (!$conn) {
            return ['success' => false, 'message' => 'Error de conexión'];
        }

        try {
            $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
            $stmt->bindParam(':usuario', $data['username']);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($data['password'], $usuario['contrasena'])) {
                $_SESSION['usuario'] = $usuario['usuario'];
                return ['success' => true, 'message' => 'Inicio de sesión exitoso',
                'usuario' => $usuario['usuario']];
            } else {
                return ['success' => false, 'message' => 'Credenciales incorrectas'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error interno'];
        }
    }
}
