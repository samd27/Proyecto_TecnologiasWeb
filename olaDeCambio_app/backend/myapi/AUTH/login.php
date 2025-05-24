<?php
session_start();
header('Content-Type: application/json');
require_once '../myapi/DataBase.php';

$data = json_decode(file_get_contents("php://input"), true);
$usuario = $data['username'];
$contrasena = $data['password'];

$db = new DataBase();
$conn = $db->getConexion();

$query = "SELECT * FROM usuarios WHERE usuario = :usuario LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bindParam(':usuario', $usuario);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($contrasena, $user['contrasena'])) {
    $_SESSION['usuario'] = $user['usuario'];
    echo json_encode(["success" => true, "usuario" => $user['usuario']]);
} else {
    echo json_encode(["success" => false, "message" => "Credenciales inválidas"]);
}
?>
