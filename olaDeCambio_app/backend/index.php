<?php
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use App\CREATE\Create;
use App\READ\Read;
use App\DELETE\Delete;
use App\UPDATE\Update;
use App\AUTH\Login;
use App\AUTH\Register;

$app = AppFactory::create();
$app->setBasePath('/Proyecto_TecnologiasWeb/olaDeCambio_app/backend');
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// POST - crear
$app->post('/api/reportes', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $creador = new Create();
    $resultado = $creador->crearReporte($data);
    $payload = json_encode(['status' => $resultado ? 'ok' : 'error']);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

// GET - leer
$app->get('/api/reportes', function (Request $request, Response $response) {
    $reader = new Read();
    $reportes = $reader->obtenerReportes();
    $payload = json_encode($reportes);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

// DELETE - eliminar
$app->delete('/api/reportes/{id}', function (Request $request, Response $response, array $args) {
    $deleter = new Delete();
    $resultado = $deleter->eliminar($args['id']);
    $payload = json_encode(['status' => $resultado ? 'ok' : 'error']);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

// PUT - actualizar
$app->put('/api/reportes/{id}', function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();
    $data['id'] = $args['id'];
    $updater = new Update();
    $resultado = $updater->actualizarReporte($data);
    $payload = json_encode(['status' => $resultado ? 'ok' : 'error']);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

// POST - registro con clase
$app->post('/registro', function (Request $request, Response $response) {
    $datos = $request->getParsedBody();
    $registro = new Register();
    $resultado = $registro->registrarUsuario($datos);
    $response->getBody()->write(json_encode($resultado));
    return $response->withHeader('Content-Type', 'application/json');
});

// POST - login con clase
$app->post('/login', function (Request $request, Response $response) {
    $datos = $request->getParsedBody();
    $login = new Login();
    $resultado = $login->iniciarSesion($datos);
    $response->getBody()->write(json_encode($resultado));
    return $response->withHeader('Content-Type', 'application/json');
});

// GET - resumen dashboard
$app->get('/api/reportes/resumen', function (Request $request, Response $response) {
    $read = new Read();
    $datos = $read->obtenerResumenDashboard();
    $response->getBody()->write(json_encode($datos));
    return $response->withHeader('Content-Type', 'application/json');
});

// GET - reportes por mes
$app->get('/api/reportes/por-mes', function (Request $request, Response $response) {
    $read = new Read();
    $datos = $read->obtenerReportesPorMes();
    $response->getBody()->write(json_encode($datos));
    return $response->withHeader('Content-Type', 'application/json');
});

// GET - sesión actual
$app->get('/session', function (Request $request, Response $response) {
    session_start();
    $resultado = [
        'loggedIn' => isset($_SESSION['usuario']),
        'usuario' => $_SESSION['usuario'] ?? null
    ];
    $response->getBody()->write(json_encode($resultado));
    return $response->withHeader('Content-Type', 'application/json');
});

// GET - logout
$app->get('/logout', function (Request $request, Response $response) {
    session_start();
    session_destroy();
    $response->getBody()->write(json_encode(['success' => true, 'message' => 'Sesión cerrada']));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
