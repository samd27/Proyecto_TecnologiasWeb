<?php
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$app = AppFactory::create();
$app->setBasePath('/Proyecto_TecnologiasWeb/olaDeCambio_app/backend');
$app->addErrorMiddleware(true, true, true);

// POST - crear
$app->post('/api/reportes', function (Request $request, Response $response) {
    $rawBody = $request->getBody()->getContents();
    $data = json_decode($rawBody, true);

    require_once __DIR__ . '/myapi/CREATE/Create.php';
    $creador = new Create();
    $resultado = $creador->crearReporte($data);

    $payload = json_encode(['status' => $resultado ? 'ok' : 'error']);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

// GET - leer
$app->get('/api/reportes', function (Request $request, Response $response) {
    require_once __DIR__ . '/myapi/READ/Read.php';
    $reader = new Read();
    $reportes = $reader->obtenerReportes();

    $payload = json_encode($reportes);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

// DELETE - eliminar
$app->delete('/api/reportes/{id}', function (Request $request, Response $response, array $args) {
    require_once __DIR__ . '/myapi/DELETE/Delete.php';
    $deleter = new Delete();
    $resultado = $deleter->eliminar($args['id']);

    $payload = json_encode(['status' => $resultado ? 'ok' : 'error']);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->put('/api/reportes/{id}', function (Request $request, Response $response, array $args) {
    $rawBody = $request->getBody()->getContents();
    $data = json_decode($rawBody, true);
    $data['id'] = $args['id'];

    require_once __DIR__ . '/myapi/UPDATE/Update.php';
    $updater = new Update();
    $resultado = $updater->actualizarReporte($data);

    $payload = json_encode(['status' => $resultado ? 'ok' : 'error']);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/registro', function ($request, $response) {
    // ⚠️ Extraer contenido JSON crudo
    $body = $request->getBody()->getContents();
    $datos = json_decode($body, true);

    // Asignar a $_POST manualmente
    $_POST['usuario'] = $datos['usuario'] ?? null;
    $_POST['contrasena'] = $datos['contrasena'] ?? null;

    require_once __DIR__ . '/myapi/AUTH/register.php';

    return $response->withHeader('Content-Type', 'application/json');
});




// POST /login
$app->post('/login', function (Request $request, Response $response) {
    $datos = $request->getParsedBody();
    $_POST['username'] = $datos['username'] ?? '';
    $_POST['password'] = $datos['password'] ?? '';
    require_once __DIR__ . '/myapi/AUTH/login.php';
    return $response->withHeader('Content-Type', 'application/json');
});



$app->run();
