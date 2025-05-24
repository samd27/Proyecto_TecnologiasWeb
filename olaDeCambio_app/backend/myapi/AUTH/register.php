<?php
header('Content-Type: application/json');
require_once '../myapi/DataBase.php';

$data = json_decode(file_get_contents("php://input"), true);
$usuario = $data['username'];
$contrasena = password_hash($data['password'], PASSWORD_DEFAULT);

$db = new DataBase();
$conn = $db->getConexion();

$query = "INSERT INTO usuarios (usuario, contrasena) VALUES (:usuario, :contrasena)";
$stmt = $conn->prepare($query);

try {
    $stmt->execute([':usuario' => $usuario, ':contrasena' => $contrasena]);
    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "El usuario ya existe"]);
}
?>
