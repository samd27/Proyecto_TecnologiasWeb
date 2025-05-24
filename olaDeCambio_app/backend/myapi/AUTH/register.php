<?php
require_once __DIR__ . '/../DataBase.php';


header('Content-Type: application/json');

// Recibir datos
$usuario = $_POST['usuario'] ?? null;
$contrasena = $_POST['contrasena'] ?? null;

if (!$usuario || !$contrasena) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos']);
    exit;
}

try {
    // Conexión con tu método
    $db = new DataBase();
    $conn = $db->getConexion();

    // Verificar si el usuario ya existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE usuario = ?");
    $stmt->execute([$usuario]);

    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'El usuario ya existe']);
        exit;
    }

    // Registrar usuario
    $hashed = password_hash($contrasena, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO usuarios (usuario, contrasena) VALUES (?, ?)");
    $stmt->execute([$usuario, $hashed]);

    echo json_encode(['success' => true, 'message' => 'Registro exitoso']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en base de datos']);
}
